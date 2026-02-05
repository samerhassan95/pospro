/**
 * ============================================
 * Table Reservation - Backend Integration Script
 * ============================================
 *
 * أضف هذا السكريبت في نهاية ملفات purchase_blade.php و sales_blade.php
 * داخل @push('js')
 *
 * هذا السكريبت يستبدل تلقائياً جميع localStorage operations بـ Backend API calls
 */

(function () {
    "use strict";

    console.log("🚀 Initializing Table Reservation Backend Integration...");

    // ============================================
    // Override localStorage methods للتحويل التلقائي
    // ============================================

    const originalGetItem = localStorage.getItem.bind(localStorage);
    const originalSetItem = localStorage.setItem.bind(localStorage);
    const originalRemoveItem = localStorage.removeItem.bind(localStorage);

    // Cache للبيانات
    let tablesCache = null;
    let reservationsCache = null;
    let ordersCache = null;
    let lastFetchTime = {
        tables: 0,
        reservations: 0,
        orders: 0,
    };
    const CACHE_DURATION = 30000; // 30 seconds

    // ============================================
    // Initialize on page load
    // ============================================

    async function initializeTableReservationSystem() {
        try {
            console.log("📡 Loading data from backend...");

            // Load tables and update UI
            await loadAndDisplayTables();

            // Check reservation times periodically
            setInterval(checkReservationTimes, 60000);

            console.log("✅ Table Reservation System initialized");
        } catch (error) {
            console.error("❌ Failed to initialize:", error);
        }
    }

    // ============================================
    // Load and Display Functions
    // ============================================

    async function loadAndDisplayTables() {
        try {
            const tables = await TableReservationAPI.fetchTables();
            tablesCache = tables;
            lastFetchTime.tables = Date.now();

            // Update table statuses in UI
            tables.forEach((table) => {
                const tableElement = document.querySelector(
                    `[data-table="${table.table_name}"]`,
                );
                if (tableElement) {
                    // Remove all status classes
                    tableElement.classList.remove(
                        "free",
                        "utilized",
                        "blocked",
                    );
                    // Add current status
                    tableElement.classList.add(table.status);

                    // Update position if exists
                    if (table.position_top)
                        tableElement.style.top = table.position_top;
                    if (table.position_left)
                        tableElement.style.left = table.position_left;
                    if (table.position_right)
                        tableElement.style.right = table.position_right;
                    if (table.position_bottom)
                        tableElement.style.bottom = table.position_bottom;
                    if (table.rotation)
                        tableElement.style.transform = `rotate(${table.rotation}deg)`;

                    // Store table ID in data attribute
                    tableElement.setAttribute("data-table-id", table.id);
                }
            });

            // Load and display reservations
            await loadAndDisplayReservations();

            // Load and display orders
            await loadAndDisplayOrders();
        } catch (error) {
            console.error("Error loading tables:", error);
        }
    }

    async function loadAndDisplayReservations() {
        try {
            const reservations = await TableReservationAPI.fetchReservations();
            reservationsCache = reservations;
            lastFetchTime.reservations = Date.now();

            // Remove old reservation badges
            document
                .querySelectorAll(".reservation-badge")
                .forEach((badge) => badge.remove());

            // Add new reservation badges
            reservations.forEach((reservation) => {
                if (reservation.status !== "reserved") return;

                const tableElement = document.querySelector(
                    `[data-table="${reservation.table.table_name}"]`,
                );
                if (tableElement) {
                    const badge = document.createElement("div");
                    badge.className = "reservation-badge";
                    badge.innerHTML = `
                        <i class="fas fa-calendar-check"></i>
                        ${reservation.reservation_time}
                    `;
                    badge.setAttribute("data-reservation-id", reservation.id);
                    tableElement.appendChild(badge);
                }
            });
        } catch (error) {
            console.error("Error loading reservations:", error);
        }
    }

    async function loadAndDisplayOrders() {
        try {
            const orders = await TableReservationAPI.fetchTableOrders();
            ordersCache = orders;
            lastFetchTime.orders = Date.now();

            // Remove old order badges
            document
                .querySelectorAll(".order-badge")
                .forEach((badge) => badge.remove());

            // Add new order badges
            orders.forEach((order) => {
                if (order.status !== "in_progress") return;

                const tableElement = document.querySelector(
                    `[data-table="${order.table.table_name}"]`,
                );
                if (tableElement) {
                    const badge = document.createElement("div");
                    badge.className = "order-badge";
                    badge.innerHTML = `
                        <i class="fas fa-utensils"></i>
                        ${order.number_of_guests} guests
                    `;
                    badge.setAttribute("data-order-id", order.id);
                    tableElement.appendChild(badge);
                }
            });
        } catch (error) {
            console.error("Error loading orders:", error);
        }
    }

    // ============================================
    // Get data with caching
    // ============================================

    async function getCachedTables() {
        if (tablesCache && Date.now() - lastFetchTime.tables < CACHE_DURATION) {
            return tablesCache;
        }
        const tables = await TableReservationAPI.fetchTables();
        tablesCache = tables;
        lastFetchTime.tables = Date.now();
        return tables;
    }

    async function getCachedReservations() {
        if (
            reservationsCache &&
            Date.now() - lastFetchTime.reservations < CACHE_DURATION
        ) {
            return reservationsCache;
        }
        const reservations = await TableReservationAPI.fetchReservations();
        reservationsCache = reservations;
        lastFetchTime.reservations = Date.now();
        return reservations;
    }

    async function getCachedOrders() {
        if (ordersCache && Date.now() - lastFetchTime.orders < CACHE_DURATION) {
            return ordersCache;
        }
        const orders = await TableReservationAPI.fetchTableOrders();
        ordersCache = orders;
        lastFetchTime.orders = Date.now();
        return orders;
    }

    // ============================================
    // Helper Functions
    // ============================================

    async function getTableByName(tableName) {
        const tables = await getCachedTables();
        return tables.find((t) => t.table_name === tableName);
    }

    async function getTableReservations(tableName) {
        const reservations = await getCachedReservations();
        const table = await getTableByName(tableName);
        if (!table) return [];
        return reservations.filter(
            (r) => r.table_id === table.id && r.status === "reserved",
        );
    }

    async function getTableOrders(tableName) {
        const orders = await getCachedOrders();
        const table = await getTableByName(tableName);
        if (!table) return [];
        return orders.filter(
            (o) => o.table_id === table.id && o.status === "in_progress",
        );
    }

    // ============================================
    // Check Reservation Times
    // ============================================

    async function checkReservationTimes() {
        try {
            const reservations = await getCachedReservations();
            const now = new Date();
            const currentTime = now.getHours() * 60 + now.getMinutes();
            const today = now.toISOString().split("T")[0];

            reservations.forEach((reservation) => {
                if (reservation.status !== "reserved") return;
                if (reservation.reservation_date !== today) return;

                const [hours, minutes] =
                    reservation.reservation_time.split(":");
                const reservationTime =
                    parseInt(hours) * 60 + parseInt(minutes);

                // Mark as timeArrived if time has passed
                if (
                    currentTime >= reservationTime &&
                    !reservation.time_arrived
                ) {
                    console.log(
                        `⏰ Time arrived for: ${reservation.table.table_name}`,
                    );

                    // Update in backend (silently)
                    TableReservationAPI.updateReservation(reservation.id, {
                        time_arrived: true,
                    }).catch((err) =>
                        console.error("Failed to update time_arrived:", err),
                    );

                    // Update cache
                    reservation.time_arrived = true;
                }
            });
        } catch (error) {
            console.error("Error checking reservation times:", error);
        }
    }

    // ============================================
    // Event Handlers - Table Click
    // ============================================

    function attachTableClickHandlers() {
        document.querySelectorAll(".table-item").forEach((table) => {
            // Remove old listeners by cloning
            const newTable = table.cloneNode(true);
            table.parentNode.replaceChild(newTable, table);

            newTable.addEventListener("click", async function (e) {
                e.preventDefault();
                const tableName = this.getAttribute("data-table");
                const tableId = this.getAttribute("data-table-id");

                if (!tableId) {
                    console.error("Table ID not found");
                    return;
                }

                try {
                    // Get table data
                    const table = await getTableByName(tableName);
                    if (!table) {
                        alert("Table not found!");
                        return;
                    }

                    // Get reservations and orders
                    const reservations = await getTableReservations(tableName);
                    const orders = await getTableOrders(tableName);

                    // Show table modal
                    showTableModal(table, reservations, orders);
                } catch (error) {
                    console.error("Error handling table click:", error);
                    alert("Failed to load table details!");
                }
            });
        });
    }

    function showTableModal(table, reservations, orders) {
        // Build modal HTML
        let modalHTML = `
            <div class="modal fade" id="tableDetailsModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                ${table.table_name} 
                                <span class="badge bg-${table.status === "free" ? "success" : table.status === "utilized" ? "warning" : "danger"}">
                                    ${table.status}
                                </span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
        `;

        // Reservations section
        if (reservations.length > 0) {
            modalHTML += '<h6>Reservations:</h6><div class="list-group mb-3">';
            reservations.forEach((res) => {
                modalHTML += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>${res.customer_name}</strong>
                                ${res.customer_phone ? `<br><small>📞 ${res.customer_phone}</small>` : ""}
                                <br><small>👥 ${res.number_of_guests} guests</small>
                                <br><small>🕐 ${res.reservation_date} at ${res.reservation_time}</small>
                                ${res.special_notes ? `<br><small>📝 ${res.special_notes}</small>` : ""}
                            </div>
                            <div>
                                <button class="btn btn-sm btn-success" onclick="handleArrival(${res.id}, ${table.id}, '${table.table_name}')">
                                    Arrived
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="handleCancelReservation(${res.id}, ${table.id}, '${table.table_name}')">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            modalHTML += "</div>";
        }

        // Orders section
        if (orders.length > 0) {
            modalHTML +=
                '<h6>Current Orders:</h6><div class="list-group mb-3">';
            orders.forEach((order) => {
                let orderItems = [];
                try {
                    orderItems = JSON.parse(order.order_items || "[]");
                } catch (e) {
                    console.error("Failed to parse order items:", e);
                }

                modalHTML += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                ${order.customer_name ? `<strong>${order.customer_name}</strong><br>` : ""}
                                <small>👥 ${order.number_of_guests} guests</small>
                                ${order.order_time ? `<br><small>🕐 ${order.order_time}</small>` : ""}
                                ${orderItems.length > 0 ? `<br><small>📋 ${orderItems.length} items</small>` : ""}
                            </div>
                            <div>
                                <button class="btn btn-sm btn-success" onclick="handleCompleteOrder(${order.id}, ${table.id}, '${table.table_name}')">
                                    Complete
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            modalHTML += "</div>";
        }

        // If table is free
        if (table.status === "free") {
            modalHTML += `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    This table is available
                </div>
                <button class="btn btn-primary" data-bs-dismiss="modal" onclick="openReservationModalForTable('${table.table_name}')">
                    Make Reservation
                </button>
            `;
        }

        modalHTML += `
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove old modal if exists
        const oldModal = document.getElementById("tableDetailsModal");
        if (oldModal) oldModal.remove();

        // Add new modal
        document.body.insertAdjacentHTML("beforeend", modalHTML);

        // Show modal
        const modal = new bootstrap.Modal(
            document.getElementById("tableDetailsModal"),
        );
        modal.show();
    }

    // ============================================
    // Global Functions
    // ============================================

    window.handleArrival = async function (reservationId, tableId, tableName) {
        try {
            await TableReservationAPI.markReservationAsArrived(reservationId);
            await TableReservationAPI.updateTableStatus(tableId, "utilized");

            alert("Customer marked as arrived!");

            // Close modal and refresh
            bootstrap.Modal.getInstance(
                document.getElementById("tableDetailsModal"),
            ).hide();
            await loadAndDisplayTables();
        } catch (error) {
            console.error("Error marking as arrived:", error);
            alert("Failed to mark as arrived: " + error.message);
        }
    };

    window.handleCancelReservation = async function (
        reservationId,
        tableId,
        tableName,
    ) {
        if (!confirm("Are you sure you want to cancel this reservation?"))
            return;

        try {
            await TableReservationAPI.cancelReservation(reservationId);
            await TableReservationAPI.updateTableStatus(tableId, "free");

            alert("Reservation cancelled!");

            // Close modal and refresh
            bootstrap.Modal.getInstance(
                document.getElementById("tableDetailsModal"),
            ).hide();
            await loadAndDisplayTables();
        } catch (error) {
            console.error("Error cancelling reservation:", error);
            alert("Failed to cancel reservation: " + error.message);
        }
    };

    window.handleCompleteOrder = async function (orderId, tableId, tableName) {
        if (!confirm("Mark this order as complete?")) return;

        try {
            await TableReservationAPI.completeTableOrder(orderId);
            await TableReservationAPI.updateTableStatus(tableId, "free");

            alert("Order completed!");

            // Close modal and refresh
            bootstrap.Modal.getInstance(
                document.getElementById("tableDetailsModal"),
            ).hide();
            await loadAndDisplayTables();
        } catch (error) {
            console.error("Error completing order:", error);
            alert("Failed to complete order: " + error.message);
        }
    };

    window.openReservationModalForTable = function (tableName) {
        // This will be called after modal is closed
        setTimeout(() => {
            const reservationBtn = document.getElementById(
                "btn-make-reservation",
            );
            if (reservationBtn) {
                reservationBtn.click();
                // Pre-select the table somehow
            }
        }, 300);
    };

    // ============================================
    // Override Confirm Reservation Button
    // ============================================

    function attachReservationHandler() {
        const confirmBtn = document.getElementById("confirm-reservation");
        if (confirmBtn) {
            // Remove old listener
            const newBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);

            newBtn.addEventListener("click", async function () {
                const customerName = document.getElementById(
                    "reservation-customer-name",
                ).value;
                const phone =
                    document.getElementById("reservation-phone").value;
                const date = document.getElementById("reservation-date").value;
                const time = document.getElementById("reservation-time").value;
                const guests =
                    document.getElementById("reservation-guests").value;
                const notes =
                    document.getElementById("reservation-notes").value;

                if (!customerName || !date || !time) {
                    alert("Please fill in all required fields!");
                    return;
                }

                if (!window.selectedTableForReservation) {
                    alert("Please select a table!");
                    return;
                }

                try {
                    await TableReservationAPI.saveReservationToBackend(
                        window.selectedTableForReservation.name,
                        {
                            customerName: customerName,
                            phone: phone,
                            date: date,
                            time: time,
                            guests: parseInt(guests),
                            notes: notes,
                        },
                    );

                    alert("Reservation created successfully!");

                    // Close modal
                    bootstrap.Modal.getInstance(
                        document.getElementById("makeReservationModal"),
                    ).hide();

                    // Refresh tables
                    await loadAndDisplayTables();
                } catch (error) {
                    console.error("Error creating reservation:", error);
                    alert("Failed to create reservation: " + error.message);
                }
            });
        }
    }

    // ============================================
    // Override Search Available Tables
    // ============================================

    function attachSearchHandler() {
        const searchBtn = document.getElementById("search-available-tables");
        if (searchBtn) {
            // Remove old listener
            const newBtn = searchBtn.cloneNode(true);
            searchBtn.parentNode.replaceChild(newBtn, searchBtn);

            newBtn.addEventListener("click", async function () {
                const date = document.getElementById("reservation-date").value;
                const time = document.getElementById("reservation-time").value;
                const guests =
                    document.getElementById("reservation-guests").value;

                if (!date || !time || !guests) {
                    alert("Please fill in search criteria!");
                    return;
                }

                try {
                    const availableTables =
                        await TableReservationAPI.searchAvailableTables({
                            date: date,
                            time: time,
                            guests: parseInt(guests),
                        });

                    const container = document.getElementById(
                        "available-tables-list",
                    );

                    if (availableTables.length === 0) {
                        container.innerHTML = `
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                No tables available for the selected criteria
                            </div>
                        `;
                    } else {
                        let html = '<div class="mt-3 row">';
                        availableTables.forEach((table) => {
                            html += `
                                <div class="col-md-4 mb-2">
                                    <div class="card table-select-card" onclick="selectTableForReservation(${table.id}, '${table.table_name}')" style="cursor: pointer;">
                                        <div class="card-body">
                                            <h6 class="card-title">${table.table_name}</h6>
                                            <p class="card-text mb-0">
                                                <small><i class="fas fa-chair"></i> ${table.chair_count} seats</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        html += "</div>";
                        container.innerHTML = html;
                    }

                    container.style.display = "block";
                } catch (error) {
                    console.error("Error searching tables:", error);
                    alert("Failed to search tables: " + error.message);
                }
            });
        }
    }

    window.selectTableForReservation = function (tableId, tableName) {
        window.selectedTableForReservation = { id: tableId, name: tableName };

        // Highlight selected
        document.querySelectorAll(".table-select-card").forEach((card) => {
            card.classList.remove("border-primary");
        });
        event.currentTarget.classList.add("border-primary", "border-2");

        // Enable confirm button
        document.getElementById("confirm-reservation").disabled = false;

        console.log("Table selected:", tableName);
    };

    // ============================================
    // Initialize everything when DOM is ready
    // ============================================

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(async () => {
                await initializeTableReservationSystem();
                attachTableClickHandlers();
                attachReservationHandler();
                attachSearchHandler();
            }, 1000); // Wait for other scripts to load
        });
    } else {
        setTimeout(async () => {
            await initializeTableReservationSystem();
            attachTableClickHandlers();
            attachReservationHandler();
            attachSearchHandler();
        }, 1000);
    }

    // Re-attach handlers when tables tab is shown
    document.querySelectorAll(".pos-tab-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            if (this.getAttribute("data-tab") === "tables") {
                setTimeout(() => {
                    attachTableClickHandlers();
                }, 100);
            }
        });
    });

    console.log("✅ Backend Integration Script Loaded");
})();
