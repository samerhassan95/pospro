// Manage Reservations - Backend Integration
async function openManageReservationsModal() {
    console.log('📋 Opening Manage Reservations modal...');

    const tbody = document.getElementById('reservations-table-body');
    const noReservationsMsg = document.getElementById('no-reservations-message');

    if (!tbody) {
        console.error('reservations-table-body element not found!');
        toastr.error('Error: Table body element not found. Please refresh the page.');
        return;
    }

    // Show loading
    tbody.innerHTML = '<tr><td colspan="9" class="text-center">Loading reservations...</td></tr>';
    if (tbody.closest('.table-responsive')) {
        tbody.closest('.table-responsive').style.display = 'block';
    }
    if (noReservationsMsg) {
        noReservationsMsg.style.display = 'none';
    }

    try {
        // Load reservations from backend using XMLHttpRequest
        const xhr = new XMLHttpRequest();
        xhr.open('GET', '/business/table-reservations', true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                const result = JSON.parse(xhr.responseText);
                const reservations = result.success ? result.data : [];

                console.log('📥 Loaded reservations from backend:', reservations);

                tbody.innerHTML = '';

                if (reservations.length === 0) {
                    if (tbody.closest('.table-responsive')) {
                        tbody.closest('.table-responsive').style.display = 'none';
                    }
                    if (noReservationsMsg) {
                        noReservationsMsg.style.display = 'block';
                    }
                } else {
                    if (tbody.closest('.table-responsive')) {
                        tbody.closest('.table-responsive').style.display = 'block';
                    }
                    if (noReservationsMsg) {
                        noReservationsMsg.style.display = 'none';
                    }

                    reservations.forEach(reservation => {
                        const row = document.createElement('tr');

                        // Determine status display
                        let statusDisplay = reservation.status;
                        let statusClass = 'text-warning';
                        
                        if (reservation.status === 'reserved') {
                            statusDisplay = '⏰ Reserved';
                            statusClass = 'text-warning';
                        } else if (reservation.status === 'arrived') {
                            statusDisplay = '✓ Arrived';
                            statusClass = 'text-success';
                        } else if (reservation.status === 'cancelled') {
                            statusDisplay = '✗ Cancelled';
                            statusClass = 'text-danger';
                        } else if (reservation.status === 'completed') {
                            statusDisplay = '✓ Completed';
                            statusClass = 'text-info';
                        }

                        const tableName = reservation.table_name || 'Unknown';

                        row.innerHTML = `
                            <td><strong>${tableName}</strong></td>
                            <td>${reservation.customer_name}</td>
                            <td>${reservation.customer_phone || 'N/A'}</td>
                            <td>${reservation.reservation_date}</td>
                            <td>${reservation.reservation_time}</td>
                            <td>${reservation.number_of_guests}</td>
                            <td>${reservation.special_notes || '-'}</td>
                            <td class="${statusClass}">${statusDisplay}</td>
                            <td>
                                ${reservation.status === 'reserved' ? `
                                    <button class="btn btn-sm btn-success mark-arrived" data-id="${reservation.id}">
                                        Mark Arrived
                                    </button>
                                    <button class="btn btn-sm btn-danger cancel-reservation" data-id="${reservation.id}" data-table="${tableName}">
                                        Cancel
                                    </button>
                                ` : '-'}
                            </td>
                        `;
                        tbody.appendChild(row);
                    });

                    // Add mark arrived functionality
                    document.querySelectorAll('.mark-arrived').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const reservationId = this.getAttribute('data-id');
                            
                            const xhr2 = new XMLHttpRequest();
                            xhr2.open('POST', `/business/table-reservations/${reservationId}/guest-arrived`, true);
                            xhr2.setRequestHeader('Content-Type', 'application/json');
                            xhr2.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
                            xhr2.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                            
                            xhr2.onload = function() {
                                if (xhr2.status >= 200 && xhr2.status < 300) {
                                    toastr.success('Guest marked as arrived!');
                                    openManageReservationsModal(); // Reload list
                                } else {
                                    toastr.error('Error marking guest as arrived');
                                }
                            };
                            
                            xhr2.send();
                        });
                    });

                    // Add cancel functionality
                    document.querySelectorAll('.cancel-reservation').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const reservationId = this.getAttribute('data-id');
                            const tableName = this.getAttribute('data-table');
                            
                            if (confirm(`Are you sure you want to cancel the reservation for table ${tableName}?`)) {
                                const xhr3 = new XMLHttpRequest();
                                xhr3.open('POST', `/business/table-reservations/${reservationId}/cancel`, true);
                                xhr3.setRequestHeader('Content-Type', 'application/json');
                                xhr3.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
                                xhr3.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                                
                                xhr3.onload = function() {
                                    if (xhr3.status >= 200 && xhr3.status < 300) {
                                        toastr.success('Reservation cancelled successfully!');
                                        openManageReservationsModal(); // Reload list
                                    } else {
                                        toastr.error('Error cancelling reservation');
                                    }
                                };
                                
                                xhr3.send();
                            }
                        });
                    });
                }
            } else {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error loading reservations. Please try again.</td></tr>';
                toastr.error('Error loading reservations');
            }
        };
        
        xhr.onerror = function() {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error loading reservations. Please try again.</td></tr>';
            toastr.error('Error loading reservations');
        };
        
        xhr.send();
    } catch (error) {
        console.error('Error loading reservations:', error);
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error loading reservations. Please try again.</td></tr>';
        toastr.error('Error loading reservations');
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const btnManageReservations = document.getElementById('btn-manage-reservations');
    if (btnManageReservations) {
        btnManageReservations.addEventListener('click', openManageReservationsModal);
        console.log('✅ Manage Reservations button initialized');
    }
});
