// currency format
function currencyFormat(amount, type = "icon", decimals = 2) {
    let symbol = $("#currency_symbol").val();
    let position = $("#currency_position").val();
    let code = $("#currency_code").val();

    // SAR Symbol SVG
    const sarSymbolSVG = '<svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-left: 3px;"><g clip-path="url(#clip0_price_5-1)"><path d="M6.68122 10.6309C6.48962 11.0558 6.36297 11.5168 6.31445 12.0003L10.369 11.1384C10.5606 10.7137 10.6872 10.2525 10.7358 9.76904L6.68122 10.6309Z" fill="currentColor"/><path d="M10.3691 8.55619C10.5607 8.13144 10.6873 7.67031 10.7359 7.18683L7.57749 7.85857V6.56725L10.369 5.97403C10.5606 5.54929 10.6873 5.08815 10.7358 4.60467L7.57739 5.27584V0.631863C7.09343 0.903594 6.66363 1.2653 6.31425 1.69195V5.54441L5.05111 5.8129V0.000244141C4.56715 0.27188 4.13735 0.633678 3.78797 1.06033V6.08129L0.961685 6.68186C0.770089 7.1066 0.643345 7.56773 0.594729 8.05122L3.78797 7.3726V8.99879L0.365788 9.72601C0.174192 10.1508 0.0475433 10.6119 -0.000976562 11.0954L3.58109 10.3341C3.87269 10.2735 4.12331 10.1011 4.28625 9.86384L4.94318 8.8899V8.88971C5.01138 8.78895 5.05111 8.66746 5.05111 8.53661V7.10412L6.31425 6.83564V9.41827L10.369 8.55599L10.3691 8.55619Z" fill="currentColor"/></g><defs><clipPath id="clip0_price_5-1"><rect width="10.7368" height="12" fill="white"/></clipPath></defs></svg>';
    
    // Check if currency is SAR
    const isSAR = code === 'SAR' || symbol === '^';

    let formattedAmount = formatNumber(amount, decimals); // Abbreviate number

    // Apply currency format based on the position and type
    if (type == "icon" || type == "symbol") {
        if (isSAR) {
            return position == "right"
                ? formattedAmount + sarSymbolSVG
                : sarSymbolSVG + formattedAmount;
        } else {
            return position == "right"
                ? formattedAmount + symbol
                : symbol + formattedAmount;
        }
    } else {
        return position == "right"
            ? formattedAmount + " " + code
            : code + " " + formattedAmount;
    }
}

// Calendar Slider for Purchase Due - Week View
(function() {
    let currentWeekStart = getWeekStart(new Date());
    let selectedDate = new Date();
    
    function getWeekStart(date) {
        const d = new Date(date);
        const day = d.getDay();
        const diff = d.getDate() - day + (day === 0 ? -6 : 1); // Monday as first day
        return new Date(d.setDate(diff));
    }
    
    function initCalendar() {
        const slider = document.getElementById('calendar-days-slider');
        if (!slider) return;
        
        renderWeek();
        updateSelectedDisplay();
        
        document.getElementById('cal-prev')?.addEventListener('click', () => {
            currentWeekStart.setDate(currentWeekStart.getDate() - 7);
            renderWeek();
        });
        
        document.getElementById('cal-next')?.addEventListener('click', () => {
            currentWeekStart.setDate(currentWeekStart.getDate() + 7);
            renderWeek();
        });
    }
    
    function renderWeek() {
        const slider = document.getElementById('calendar-days-slider');
        const monthLabel = document.getElementById('cal-month-label');
        if (!slider || !monthLabel) return;
        
        // Update month label based on week
        monthLabel.textContent = currentWeekStart.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        let html = '';
        
        // Render 7 days starting from currentWeekStart
        for (let i = 0; i < 7; i++) {
            const date = new Date(currentWeekStart);
            date.setDate(currentWeekStart.getDate() + i);
            
            const isToday = date.toDateString() === today.toDateString();
            const isSelected = date.toDateString() === selectedDate.toDateString();
            
            let classes = 'cal-day';
            if (isToday) classes += ' today';
            if (isSelected && !isToday) classes += ' selected';
            
            const year = date.getFullYear();
            const month = date.getMonth() + 1;
            const day = date.getDate();
            
            html += `<button class="${classes}" data-date="${year}-${month}-${day}">${day}</button>`;
        }
        
        slider.innerHTML = html;
        
        // Add click handlers
        slider.querySelectorAll('.cal-day').forEach(btn => {
            btn.addEventListener('click', function() {
                const [y, m, d] = this.dataset.date.split('-').map(Number);
                selectedDate = new Date(y, m - 1, d);
                renderWeek();
                updateSelectedDisplay();
            });
        });
    }
    
    function updateSelectedDisplay() {
        const dayEl = document.getElementById('selected-day');
        const weekdayEl = document.getElementById('selected-weekday');
        const monthYearEl = document.getElementById('selected-month-year');
        
        if (dayEl) dayEl.textContent = selectedDate.getDate().toString().padStart(2, '0');
        if (weekdayEl) weekdayEl.textContent = selectedDate.toLocaleDateString('en-US', { weekday: 'short' });
        if (monthYearEl) monthYearEl.textContent = selectedDate.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
    }
    
    document.addEventListener('DOMContentLoaded', initCalendar);
})();

// Update design when a single business content exists
document.addEventListener("DOMContentLoaded", function () {
    // Select the container, ensure it exists
    const container = document.querySelector(".business-stat");
    if (container) {
        const businessContents =
            container.querySelectorAll(".business-content");
        const customImageBg = document.querySelector(".custom-image-bg");

        // Dynamically set column class based on the number of business content elements
        container.classList.add(`columns-${businessContents.length}`);

        if (businessContents.length == 1) {
            businessContents[0].style.padding = "3% 2%";
            if (customImageBg) {
                customImageBg.style.padding = "2%";
            }
            businessContents[0].style.borderRadius = "0";
        }
    }
});

// Initialize loading state and load dashboard data
$(document).ready(function() {
    showBusinessDashboardLoading();
    
    // Load all dashboard data
    Promise.all([
        getDashboardData(),
        getYearlyStatistics(),
        fetchTaskData()
    ]).finally(() => {
        // Hide loading overlay after all data is loaded
        hideBusinessDashboardLoading();
    });
});

// Business Dashboard Loading functions - COMMENTED OUT
/*
function showBusinessDashboardLoading() {
    $('#dashboard-loading-overlay').removeClass('hidden');
}

function hideBusinessDashboardLoading() {
    setTimeout(() => {
        $('#dashboard-loading-overlay').addClass('hidden');
    }, 500); // Small delay for smooth transition
}
*/

function getDashboardData() {
    var url = $("#get-dashboard").val();
    return $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        success: function (res) {
            $("#total_sales").text(res.total_sales);
            $("#this_month_total_sales").text(res.this_month_total_sales);
            $("#total_purchase").text(res.total_purchase);
            $("#this_month_total_purchase").text(res.this_month_total_purchase);
            $("#total_income").text(res.total_income);
            $("#this_month_total_income").text(res.this_month_total_income);
            $("#total_expense").text(res.total_expense);
            $("#this_month_total_expense").text(res.this_month_total_expense);
            $("#total_customer").text(res.total_customer);
            $("#this_month_total_customer").text(res.this_month_total_customer);
            $("#total_supplier").text(res.total_supplier);
            $("#this_month_total_supplier").text(res.this_month_total_supplier);
            $("#total_sales_return").text(res.total_sales_return);
            $("#this_month_total_sale_return").text(
                res.this_month_total_sale_return
            );
            $("#total_purchase_return").text(res.total_purchase_return);
            $("#this_month_total_purchase_return").text(
                res.this_month_total_purchase_return
            );
        },
        error: function(xhr, status, error) {
            console.error("Business dashboard data loading failed:", error);
            // Show error state or fallback values
            const statElements = [
                '#total_sales', '#total_purchase', '#total_income', '#total_expense',
                '#total_customer', '#total_supplier', '#total_sales_return', '#total_purchase_return'
            ];
            
            statElements.forEach(element => {
                $(element).text('--');
            });
        }
    });
}

// Function to abbreviate numbers (K, M, B)
function formatNumber(number, decimals = 2) {
    if (number >= 1e9) {
        return removeTrailingZeros((number / 1e9).toFixed(decimals)) + "B";
    } else if (number >= 1e6) {
        return removeTrailingZeros((number / 1e6).toFixed(decimals)) + "M";
    } else if (number >= 1e3) {
        return removeTrailingZeros((number / 1e3).toFixed(decimals)) + "K";
    } else {
        return removeTrailingZeros(number.toFixed(decimals));
    }
}

function removeTrailingZeros(value) {
    return parseFloat(value).toString();
}

// Revenue chart----------------->
let revenueChart;
const ctxRevenue = document.getElementById("revenueChart").getContext("2d");
function totalEarningExpenseChart(total_loss, total_profit) {
    if (revenueChart) {
        revenueChart.destroy();
    }

    revenueChart = new Chart(ctxRevenue, {
        type: "line",
        data: {
            labels: [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "May",
                "Jun",
                "Jul",
                "Aug",
                "Sep",
                "Oct",
                "Nov",
                "Dec",
            ],
            datasets: [
                {
                    label: "Profit",
                    data: total_profit,
                    borderColor: "#A507FF",
                    borderWidth: 4,
                    fill: false,
                    pointRadius: 1,
                    pointHoverRadius: 6,
                    tension: 0.4,
                },
                {
                    label: "Loss",
                    data: total_loss,
                    borderColor: "#FF3B30",
                    borderWidth: 4,
                    fill: false,
                    pointRadius: 1,
                    pointHoverRadius: 6,
                    tension: 0.4,
                },
            ],
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    enabled: true,
                    backgroundColor: "white",
                    borderColor: "#ddd",
                    borderWidth: 1,
                    titleColor: "#000",
                    bodyColor: "#000",
                    callbacks: {
                        title: function (context) {
                            const month = context[0].label;
                            return `${month}`;
                        },
                        label: function (context) {
                            const value = context.raw;
                            const label = context.dataset.label;
                            return `${label}: ${Math.abs(
                                value
                            ).toLocaleString()}`;
                        },
                    },
                    padding: 8,
                    displayColors: false,
                },
                legend: {
                    display: false,
                },
            },

            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return formatNumber(value);
                        },
                    },
                    grid: {
                        drawBorder: false,
                        color: "#C2C6CE",
                        borderDash: [4, 4],
                    },
                },
            },

            layout: {
                padding: {
                    left: 10,
                    right: 10,
                    top: 10,
                    bottom: 10,
                },
            },
            hover: {
                mode: "nearest",
                intersect: true,
            },
        },
    });
}

// Function to get yearly statistics and update the chart
function getYearlyStatistics(year = new Date().getFullYear()) {
    const url = $("#revenue-statistic").val() + "?year=" + year;

    return $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        success: function (res) {
            const loss = res.loss;
            const profit = res.profit;
            const total_loss = [];
            const total_profit = [];

            for (let i = 1; i <= 12; i++) {
                const monthName = getMonthNameFromIndex(i);

                total_loss[i - 1] = loss
                    .filter((item) => item.month == monthName)
                    .reduce((sum, item) => sum + item.total, 0);

                total_profit[i - 1] = profit
                    .filter((item) => item.month == monthName)
                    .reduce((sum, item) => sum + item.total, 0);
            }


            // Update chart with the new data
            totalEarningExpenseChart(total_loss, total_profit);

            const loss_value = total_loss.reduce(
                (sum, value) => sum + value,
                0
            );
            const profit_value = total_profit.reduce(
                (sum, value) => sum + value,
                0
            );

            document.querySelector(
                ".loss-value"
            ).textContent = `${currencyFormat(loss_value)}`;
            document.querySelector(
                ".profit-value"
            ).textContent = `${currencyFormat(profit_value)}`;
        },
        error: function (err) {
            console.error("Error fetching revenue data:", err);
            $('.profit-value, .loss-value').text('--');
        },
    });
}

// Function to convert month index to month name
function getMonthNameFromIndex(index) {
    const months = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
    ];
    return months[index - 1];
}

// Handle year change event
$(".revenue-year").on("change", function () {
    const year = $(this).val();
    getYearlyStatistics(year);
});

// Overall Reports ----------------------->
const canvas = document.getElementById("Overallreports");
const ctxOverallReports = canvas.getContext("2d");

const gradientSales = ctxOverallReports.createLinearGradient(
    0,
    0,
    0,
    canvas.height
);
gradientSales.addColorStop(0, "#8554FF");
gradientSales.addColorStop(1, "#B8A1FF");

const gradientPurchase = ctxOverallReports.createLinearGradient(
    0,
    0,
    0,
    canvas.height
);
gradientPurchase.addColorStop(0, "#FD8D00");
gradientPurchase.addColorStop(1, "#FFC694");

const gradientExpense = ctxOverallReports.createLinearGradient(
    0,
    0,
    0,
    canvas.height
);
gradientExpense.addColorStop(0, "#FF8983");
gradientExpense.addColorStop(1, "#FF3B30");

const gradientIncome = ctxOverallReports.createLinearGradient(
    0,
    0,
    0,
    canvas.height
);
gradientIncome.addColorStop(0, "#05C535");
gradientIncome.addColorStop(1, "#36F165");

// Data for the chart
const data = {
    labels: ["Purchase", "Sales", "Income", "Expense"],
    datasets: [
        {
            backgroundColor: [
                gradientPurchase,
                gradientSales,
                gradientIncome,
                gradientExpense,
            ],
            hoverOffset: 5,
        },
    ],
};

const config = {
    type: "pie",
    data: data,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false,
            },
            tooltip: {
                enabled: true,
                backgroundColor: "#FFFFFF",
                titleColor: "#000000",
                bodyColor: "#000000",
                // borderColor: "#CCCCCC",
                borderWidth: 1,
                displayColors: false,
            },
        },
    },
};

const Overallreports = new Chart(ctxOverallReports, config);

window.addEventListener("resize", function () {
    Overallreports.resize();
});

function fetchTaskData(year = new Date().getFullYear()) {
    const url = $("#get-overall-report").val() + "?year=" + year;
    
    return $.ajax({
        url: url,
        method: "GET",
        success: function (response) {
            Overallreports.data.datasets[0].data = [
                response.overall_purchase || 0.000001,
                response.overall_sale || 0.000001,
                response.overall_income || 0.000001,
                response.overall_expense || 0.000001,
            ];
            Overallreports.update();

            $("#overall_purchase").text(
                currencyFormat(response.overall_purchase)
            );
            $("#overall_sale").text(currencyFormat(response.overall_sale));
            $("#overall_income").text(currencyFormat(response.overall_income));
            $("#overall_expense").text(
                currencyFormat(response.overall_expense)
            );
        },
        error: function (error) {
            console.error("Error fetching overall report data:", error);
            $('#overall_purchase, #overall_sale, #overall_income, #overall_expense').text('--');
        },
    });
}

$(".overview-year").on("change", function () {
    const year = $(this).val();
    fetchTaskData(year);
});
