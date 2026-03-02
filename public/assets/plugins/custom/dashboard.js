// Get CSS variable colors
function getCSSColor(variable) {
    return getComputedStyle(document.documentElement).getPropertyValue(variable).trim();
}

// currency format
function currencyFormat(amount, type = "icon", decimals = 2) {
    let symbol = $('#currency_symbol').val();
    let position = $('#currency_position').val();
    let code = $('#currency_code').val();

    // Handle null, undefined, or non-numeric values
    if (amount === null || amount === undefined || isNaN(amount)) {
        amount = 0;
    }

    // SAR Symbol SVG
    const sarSymbolSVG = '<svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-left: 3px;"><g clip-path="url(#clip0_price_5-1)"><path d="M6.68122 10.6309C6.48962 11.0558 6.36297 11.5168 6.31445 12.0003L10.369 11.1384C10.5606 10.7137 10.6872 10.2525 10.7358 9.76904L6.68122 10.6309Z" fill="currentColor"/><path d="M10.3691 8.55619C10.5607 8.13144 10.6873 7.67031 10.7359 7.18683L7.57749 7.85857V6.56725L10.369 5.97403C10.5606 5.54929 10.6873 5.08815 10.7358 4.60467L7.57739 5.27584V0.631863C7.09343 0.903594 6.66363 1.2653 6.31425 1.69195V5.54441L5.05111 5.8129V0.000244141C4.56715 0.27188 4.13735 0.633678 3.78797 1.06033V6.08129L0.961685 6.68186C0.770089 7.1066 0.643345 7.56773 0.594729 8.05122L3.78797 7.3726V8.99879L0.365788 9.72601C0.174192 10.1508 0.0475433 10.6119 -0.000976562 11.0954L3.58109 10.3341C3.87269 10.2735 4.12331 10.1011 4.28625 9.86384L4.94318 8.8899V8.88971C5.01138 8.78895 5.05111 8.66746 5.05111 8.53661V7.10412L6.31425 6.83564V9.41827L10.369 8.55599L10.3691 8.55619Z" fill="currentColor"/></g><defs><clipPath id="clip0_price_5-1"><rect width="10.7368" height="12" fill="white"/></clipPath></defs></svg>';
    
    // Check if currency is SAR
    const isSAR = code === 'SAR' || symbol === '^';

    let formatted_amount = formattedAmount(amount, decimals);

    // Apply currency format based on the position and type
    if (type === "icon" || type === "symbol") {
        if (isSAR) {
            if (position === "right") {
                return formatted_amount + sarSymbolSVG;
            } else {
                return sarSymbolSVG + formatted_amount;
            }
        } else {
            if (position === "right") {
                return formatted_amount + symbol;
            } else {
                return symbol + formatted_amount;
            }
        }
    } else {
        if (position === "right") {
            return formatted_amount + ' ' + code;
        } else {
            return code + ' ' + formatted_amount;
        }
    }
}

// Function to abbreviate numbers (K, M, B)
function formattedAmount(number, decimals = 2) {
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

$(document).ready(function () {
    // Show loading overlay initially
    showDashboardLoading();
    
    // Load all dashboard data
    Promise.all([
        getDashboardData(),
        getYearlySubscriptions(),
        bestPlanSubscribes()
    ]).finally(() => {
        // Hide loading overlay after all data is loaded
        hideDashboardLoading();
    });
});

// Loading functions
function showDashboardLoading() {
    const overlay = $('#dashboard-loading-overlay');
    if (overlay.length) {
        overlay.removeClass('hidden');
    }
}

function hideDashboardLoading() {
    const overlay = $('#dashboard-loading-overlay');
    if (overlay.length) {
        setTimeout(() => {
            overlay.addClass('hidden');
        }, 500);
    }
}

function getDashboardData() {
    var url = $("#get-dashboard").val();
    return $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        success: function (res) {
            // Update statistics with actual data
            $("#total_businesses").text(res.total_businesses);
            $("#expired_businesses").text(res.expired_businesses);
            $("#plan_subscribes").text(res.plan_subscribes);
            $("#business_categories").text(res.business_categories);
            $("#total_plans").text(res.total_plans);
        },
        error: function(xhr, status, error) {
            console.error("Dashboard data loading failed:", error);
            // Show error state or fallback values
            $("#total_businesses, #expired_businesses, #plan_subscribes, #business_categories, #total_plans").text('--');
        }
    });
}

$(".overview-year").on("change", function () {
    let year = $(this).val();
    bestPlanSubscribes(year);
});

$(".yearly-statistics").on("change", function () {
    let year = $(this).val();
    getYearlySubscriptions(year);
});

function getYearlySubscriptions(year = new Date().getFullYear()) {
    var url = $("#yearly-subscriptions-url").val();
    
    if (!url) {
        console.warn('Yearly subscriptions URL not found');
        // Show empty chart
        const emptyData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        subscriptionChart(emptyData);
        $(".income-value").html(currencyFormat(0));
        return Promise.resolve();
    }
    
    return $.ajax({
        type: "GET",
        url: url + "?year=" + year,
        dataType: "json",
        success: function (res) {
            var subscriptions = [];
            let totalAmount = 0;

            // Handle empty or null response
            if (!res || !Array.isArray(res)) {
                res = [];
            }

            for (var i = 0; i <= 11; i++) {
                var monthName = getMonthNameFromIndex(i);
                var subscriptionsData = res.find((item) => {
                    return item.month === monthName;
                });

                subscriptions[i] = subscriptionsData
                    ? parseFloat(subscriptionsData.total_amount) || 0
                    : 0;

                totalAmount += subscriptions[i];
            }

            // Always show chart, even with zero data
            subscriptionChart(subscriptions);
            $(".income-value").html(currencyFormat(totalAmount));
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
            console.error("Response:", xhr.responseText);
            // Show empty chart on error
            const emptyData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            subscriptionChart(emptyData);
            $(".income-value").html(currencyFormat(0));
        },
    });
}

let userOverView = false;

// Function to update the User Overview chart
function bestPlanSubscribes(year = new Date().getFullYear()) {
    if (userOverView) {
        userOverView.destroy();
    }

    Chart.defaults.datasets.doughnut.cutout = "65%";
    let url = $("#get-plans-overview").val();
    
    if (!url) {
        console.warn('Plans overview URL not found');
        // Show empty chart
        showEmptyPlansChart();
        return Promise.resolve();
    }
    
    return $.ajax({
        url: (url += "?year=" + year),
        type: "GET",
        dataType: "json",
        success: function (res) {
            var labels = [];
            var data = [];

            // Handle empty or null response
            if (!res || !Array.isArray(res)) {
                res = [];
            }

            $.each(res, function (index, planData) {
                if (planData && planData.plan && planData.plan.subscriptionName) {
                    var label = planData.plan.subscriptionName + ": " + (planData.plan_count || 0);
                    labels.push(label);
                    data.push(parseFloat(planData.plan_count) || 0);
                }
            });

            // Check if we have data
            const hasData = data.length > 0 && data.some(val => val > 0);

            if (!hasData) {
                showEmptyPlansChart();
                return;
            }

            var roundedCornersFor = {
                start: Array.from({ length: data.length }, (_, i) => i),
            };
            Chart.defaults.elements.arc.roundedCornersFor = roundedCornersFor;

            let inMonths = $("#plans-chart");
            
            // Check if canvas exists
            if (!inMonths.length) {
                console.warn('Plans chart canvas not found');
                return;
            }
            
            userOverView = new Chart(inMonths, {
                type: "doughnut",
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: "Total Users",
                            borderWidth: 0,
                            data: data,
                            backgroundColor: [
                                getCSSColor("--clr-secondary"),
                                getCSSColor("--clr-primary"),
                                getCSSColor("--clr-primary") + "80",
                                getCSSColor("--clr-secondary") + "80",
                            ],
                            borderColor: [
                                getCSSColor("--clr-secondary"),
                                getCSSColor("--clr-primary"),
                                getCSSColor("--clr-primary") + "80",
                                getCSSColor("--clr-secondary") + "80",
                            ],
                        },
                    ],
                },
                plugins: [
                    {
                        afterUpdate: function (chart) {
                            if (
                                chart.options.elements.arc.roundedCornersFor !==
                                undefined
                            ) {
                                var arcValues = Object.values(
                                    chart.options.elements.arc.roundedCornersFor
                                );

                                arcValues.forEach(function (arcs) {
                                    arcs = Array.isArray(arcs) ? arcs : [arcs];
                                    arcs.forEach(function (i) {
                                        var arc =
                                            chart.getDatasetMeta(0).data[i];
                                        arc.round = {
                                            x:
                                                (chart.chartArea.left +
                                                    chart.chartArea.right) /
                                                2,
                                            y:
                                                (chart.chartArea.top +
                                                    chart.chartArea.bottom) /
                                                2,
                                            radius:
                                                (arc.outerRadius +
                                                    arc.innerRadius) /
                                                2,
                                            thickness:
                                                (arc.outerRadius -
                                                    arc.innerRadius) /
                                                2,
                                            backgroundColor:
                                                arc.options.backgroundColor,
                                        };
                                    });
                                });
                            }
                        },
                        afterDraw: (chart) => {
                            if (
                                chart.options.elements.arc.roundedCornersFor !==
                                undefined
                            ) {
                                var { ctx, canvas } = chart;
                                var arc,
                                    roundedCornersFor =
                                        chart.options.elements.arc
                                            .roundedCornersFor;
                                for (var position in roundedCornersFor) {
                                    var values = Array.isArray(
                                        roundedCornersFor[position]
                                    )
                                        ? roundedCornersFor[position]
                                        : [roundedCornersFor[position]];
                                    values.forEach((p) => {
                                        arc = chart.getDatasetMeta(0).data[p];
                                        var startAngle =
                                            Math.PI / 2 - arc.startAngle;
                                        var endAngle =
                                            Math.PI / 2 - arc.endAngle;
                                        ctx.save();
                                        ctx.translate(arc.round.x, arc.round.y);
                                        ctx.fillStyle =
                                            arc.options.backgroundColor;
                                        ctx.beginPath();
                                        if (position == "start") {
                                            ctx.arc(
                                                arc.round.radius *
                                                    Math.sin(startAngle),
                                                arc.round.radius *
                                                    Math.cos(startAngle),
                                                arc.round.thickness,
                                                0,
                                                2 * Math.PI
                                            );
                                        } else {
                                            ctx.arc(
                                                arc.round.radius *
                                                    Math.sin(endAngle),
                                                arc.round.radius *
                                                    Math.cos(endAngle),
                                                arc.round.thickness,
                                                0,
                                                2 * Math.PI
                                            );
                                        }
                                        ctx.closePath();
                                        ctx.fill();
                                        ctx.restore();
                                    });
                                }
                            }
                        },
                    },
                ],
                options: {
                    responsive: true,
                    tooltips: {
                        displayColors: true,
                        zIndex: 999999,
                    },
                    plugins: {
                        legend: {
                            position: "top",
                            labels: {
                                usePointStyle: true,
                                padding: 10,
                            },
                        },
                    },
                    scales: {
                        x: {
                            display: false,
                            stacked: true,
                        },
                        y: {
                            display: false,
                            stacked: true,
                        },
                    },
                },
            });
        },
        error: function (xhr, textStatus, errorThrown) {
            console.error("Error fetching user overview data: " + textStatus);
            showEmptyPlansChart();
        },
    });
}

// Helper function to show empty plans chart
function showEmptyPlansChart() {
    let inMonths = $("#plans-chart");
    if (!inMonths.length) {
        console.warn('Plans chart canvas not found');
        return;
    }
    
    userOverView = new Chart(inMonths, {
        type: "doughnut",
        data: {
            labels: ['No Data'],
            datasets: [{
                label: "Total Users",
                borderWidth: 0,
                data: [0.0001],
                backgroundColor: [getCSSColor("--clr-secondary")],
                borderColor: [getCSSColor("--clr-secondary")],
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "top",
                    labels: {
                        usePointStyle: true,
                        padding: 10,
                    },
                },
                tooltip: {
                    enabled: false,
                },
            },
            scales: {
                x: {
                    display: false,
                },
                y: {
                    display: false,
                },
            },
        },
    });
}

// PRINT TOP DATA
getDashboardData();
function getDashboardData() {
    var url = $("#get-dashboard").val();
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        success: function (res) {
            $("#total_businesses").text(res.total_businesses);
            $("#expired_businesses").text(res.expired_businesses);
            $("#plan_subscribes").text(res.plan_subscribes);
            $("#business_categories").text(res.business_categories);
            $("#total_plans").text(res.total_plans);
            $("#total_staffs").text(res.total_staffs);
        },
    });
}

// Function to convert month index to month name
function getMonthNameFromIndex(index) {
    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    return monthNames[index];
}

let statiSticsValu = false;

function subscriptionChart(subscriptions) {
    if (statiSticsValu) {
        statiSticsValu.destroy();
    }

    var canvas = document.getElementById("monthly-statistics");
    if (!canvas) {
        console.warn('Monthly statistics canvas not found');
        return;
    }
    
    var ctx = canvas.getContext("2d");
    
    // Use primary color for gradient
    const primaryColor = getCSSColor('--clr-primary');
    var gradient = ctx.createLinearGradient(0, 100, 10, 280);
    gradient.addColorStop(0, primaryColor + '40'); // 25% opacity
    gradient.addColorStop(1, primaryColor + '00'); // 0% opacity

    // Ensure subscriptions is an array with 12 elements
    if (!Array.isArray(subscriptions) || subscriptions.length !== 12) {
        subscriptions = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    }

    var totals = subscriptions.reduce(function (accumulator, currentValue) {
        return accumulator + (parseFloat(currentValue) || 0);
    }, 0);

    // Check if we have any data
    const hasData = subscriptions.some(val => val > 0);

    statiSticsValu = new Chart(ctx, {
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
                    backgroundColor: gradient,
                    label: "Total Subscription Amount: " + totals,
                    fill: true,
                    borderWidth: 2,
                    borderColor: getCSSColor('--clr-primary'),
                    data: subscriptions,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            tension: 0.4,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    enabled: hasData,
                    displayColors: true,
                    backgroundColor: "#FFFFFF",
                    titleColor: "#000000",
                    bodyColor: "#000000",
                    borderColor: "rgba(0, 0, 0, 0.1)",
                    borderWidth: 1,
                    padding: 10,
                },
            },
            scales: {
                x: {
                    display: true,
                    grid: {
                        display: false,
                    },
                },
                y: {
                    display: true,
                    beginAtZero: true,
                    grid: {
                        color: "#D3D8DD",
                        borderDash: [5, 5],
                        borderDashOffset: 2,
                    },
                },
            },
            layout: {
                padding: {
                    top: 10,
                    bottom: 10,
                    left: 10,
                    right: 10,
                },
            },
        },
    });
}

window.addEventListener("resize", function () {
    if (statiSticsValu) {
        statiSticsValu.resize();
    }
});
