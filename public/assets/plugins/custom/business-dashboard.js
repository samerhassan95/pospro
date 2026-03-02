// Get CSS variable colors
function getCSSColor(variable) {
    return getComputedStyle(document.documentElement).getPropertyValue(variable).trim();
}
// Get CSS variable colors
function getCSSColor(variable) {
    return getComputedStyle(document.documentElement).getPropertyValue(variable).trim();
}

// currency format
function currencyFormat(amount, type = "icon", decimals = 2) {
    let symbol = $("#currency_symbol").val();
    let position = $("#currency_position").val();
    let code = $("#currency_code").val();

    // Handle null, undefined, or non-numeric values
    if (amount === null || amount === undefined || isNaN(amount)) {
        amount = 0;
    }

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
    // Check if we're on the dashboard page
    if (!$('#get-dashboard').length) {
        return;
    }
    
    // Load all dashboard data
    Promise.all([
        getDashboardData(),
        getYearlyStatistics(),
        fetchTaskData()
    ]).catch(error => {
        console.error('Error loading dashboard data:', error);
    });
});

function getDashboardData() {
    var url = $("#get-dashboard").val();
    
    if (!url) {
        return Promise.resolve();
    }
    
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
    // Handle null, undefined, or non-numeric values
    if (number === null || number === undefined || isNaN(number)) {
        return '0';
    }
    
    number = parseFloat(number);
    
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

function totalEarningExpenseChart(total_loss, total_profit) {
    const ctxRevenue = document.getElementById("revenueChart");
    
    // Check if canvas element exists
    if (!ctxRevenue) {
        console.warn('Revenue chart canvas not found');
        return;
    }
    
    const ctx = ctxRevenue.getContext("2d");
    
    if (revenueChart) {
        revenueChart.destroy();
    }

    // Check if data is empty (all zeros)
    const hasData = total_loss.some(val => val !== 0) || total_profit.some(val => val !== 0);
    
    // If no data, show empty chart with zeros
    const displayLoss = hasData ? total_loss : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    const displayProfit = hasData ? total_profit : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

    revenueChart = new Chart(ctx, {
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
                    data: displayProfit,
                    borderColor: getCSSColor("--clr-secondary"),
                    borderWidth: 4,
                    fill: false,
                    pointRadius: 1,
                    pointHoverRadius: 6,
                    tension: 0.4,
                },
                {
                    label: "Loss",
                    data: displayLoss,
                    borderColor: getCSSColor("--clr-primary"),
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
                    enabled: hasData,
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
    const url = $("#revenue-statistic").val();
    
    if (!url) {
        return Promise.resolve();
    }
    
    return $.ajax({
        type: "GET",
        url: url + "?year=" + year,
        dataType: "json",
        success: function (res) {
            const loss = res.loss || [];
            const profit = res.profit || [];
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

            // Update chart with the new data (will show empty chart if all zeros)
            totalEarningExpenseChart(total_loss, total_profit);

            const loss_value = total_loss.reduce(
                (sum, value) => sum + value,
                0
            );
            const profit_value = total_profit.reduce(
                (sum, value) => sum + value,
                0
            );

            // Display values or 0 if no data
            document.querySelector(
                ".loss-value"
            ).innerHTML = `${currencyFormat(loss_value)}`;
            document.querySelector(
                ".profit-value"
            ).innerHTML = `${currencyFormat(profit_value)}`;
        },
        error: function (err) {
            console.error("Error fetching revenue data:", err);
            // Show empty chart with zeros on error
            const emptyData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            totalEarningExpenseChart(emptyData, emptyData);
            $('.profit-value, .loss-value').html(currencyFormat(0));
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
function initOverallReportsChart() {
    const canvas = document.getElementById("Overallreports");
    
    // Check if canvas element exists
    if (!canvas) {
        console.warn('Overall reports chart canvas not found');
        return null;
    }
    
    const ctxOverallReports = canvas.getContext("2d");

    const gradientSales = ctxOverallReports.createLinearGradient(
        0,
        0,
        0,
        canvas.height
    );
    gradientSales.addColorStop(0, getCSSColor("--clr-secondary"));
    gradientSales.addColorStop(1, getCSSColor("--clr-secondary") + "80");

    const gradientPurchase = ctxOverallReports.createLinearGradient(
        0,
        0,
        0,
        canvas.height
    );
    gradientPurchase.addColorStop(0, getCSSColor("--clr-primary"));
    gradientPurchase.addColorStop(1, getCSSColor("--clr-primary") + "80");

    const gradientExpense = ctxOverallReports.createLinearGradient(
        0,
        0,
        0,
        canvas.height
    );
    gradientExpense.addColorStop(0, getCSSColor("--clr-secondary"));
    gradientExpense.addColorStop(1, getCSSColor("--clr-secondary") + "80");

    const gradientIncome = ctxOverallReports.createLinearGradient(
        0,
        0,
        0,
        canvas.height
    );
    gradientIncome.addColorStop(0, getCSSColor("--clr-primary"));
    gradientIncome.addColorStop(1, getCSSColor("--clr-primary") + "60");

    // Data for the chart
    const data = {
        labels: ["Purchase", "Sales", "Income", "Expense"],
        datasets: [
            {
                data: [0.000001, 0.000001, 0.000001, 0.000001],
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
                    borderWidth: 1,
                    displayColors: false,
                },
            },
        },
    };

    const chart = new Chart(ctxOverallReports, config);

    window.addEventListener("resize", function () {
        chart.resize();
    });
    
    return chart;
}

// Initialize the chart
let Overallreports = null;
if (document.getElementById("Overallreports")) {
    Overallreports = initOverallReportsChart();
}

function fetchTaskData(year = new Date().getFullYear()) {
    const url = $("#get-overall-report").val() + "?year=" + year;
    
    // Check if chart exists
    if (!Overallreports) {
        console.warn('Overall reports chart not initialized');
        return Promise.resolve();
    }
    
    return $.ajax({
        url: url,
        method: "GET",
        success: function (response) {
            // Get values or default to 0
            const purchase = response.overall_purchase || 0;
            const sale = response.overall_sale || 0;
            const income = response.overall_income || 0;
            const expense = response.overall_expense || 0;
            
            // Check if all values are zero
            const hasData = purchase > 0 || sale > 0 || income > 0 || expense > 0;
            
            // If no data, use small values to show empty chart structure
            Overallreports.data.datasets[0].data = hasData ? [
                purchase,
                sale,
                income,
                expense,
            ] : [
                0.000001,
                0.000001,
                0.000001,
                0.000001,
            ];
            Overallreports.update();

            // Display actual values (including 0)
            $("#overall_purchase").html(currencyFormat(purchase));
            $("#overall_sale").html(currencyFormat(sale));
            $("#overall_income").html(currencyFormat(income));
            $("#overall_expense").html(currencyFormat(expense));
        },
        error: function (error) {
            console.error("Error fetching overall report data:", error);
            // Show empty chart on error
            if (Overallreports) {
                Overallreports.data.datasets[0].data = [0.000001, 0.000001, 0.000001, 0.000001];
                Overallreports.update();
            }
            $('#overall_purchase, #overall_sale, #overall_income, #overall_expense').html(currencyFormat(0));
        },
    });
}

$(".overview-year").on("change", function () {
    const year = $(this).val();
    fetchTaskData(year);
});
