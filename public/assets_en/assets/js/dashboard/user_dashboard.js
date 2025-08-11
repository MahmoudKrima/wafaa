/*
    ======================================
        User Dashboard Charts | Script
    ======================================
*/

try {
    // Simple sparkline chart for Total Visits
    var spark1 = {
        chart: {
            type: 'area',
            height: 50,
            sparkline: {
                enabled: true
            },
            group: 'sparklines',
            id: 'total-users-chart'
        },
        stroke: {
            curve: 'smooth'
        },
        fill: {
            opacity: 1,
            type: 'gradient',
            gradient: {
                type: 'vertical',
                shadeIntensity: 1,
                inverseColors: !1,
                opacityFrom: 0.40,
                opacityTo: 0.05,
                stops: [45, 100]
            }
        },
        series: [{
            name: 'Visits',
            data: [28, 50, 36, 60, 38, 52, 38]
        }],
        labels: ['1', '2', '3', '4', '5', '6', '7'],
        yaxis: {
            min: 0,
            labels: {
                minWidth: 30
            }
        },
        colors: ['#8dbf42'],
        tooltip: {
            x: {
                show: false,
            }
        }
    };

    // Simple sparkline chart for Paid Visits
    var spark2 = {
        chart: {
            type: 'area',
            height: 50,
            sparkline: {
                enabled: true
            },
            group: 'sparklines',
            id: 'paid-visits-chart'
        },
        stroke: {
            curve: 'smooth'
        },
        fill: {
            opacity: 1,
            type: 'gradient',
            gradient: {
                type: 'vertical',
                shadeIntensity: 1,
                inverseColors: !1,
                opacityFrom: 0.40,
                opacityTo: 0.05,
                stops: [45, 100]
            }
        },
        series: [{
            name: 'Paid Visits',
            data: [15, 25, 18, 30, 19, 26, 19]
        }],
        labels: ['1', '2', '3', '4', '5', '6', '7'],
        yaxis: {
            min: 0,
            labels: {
                minWidth: 30
            }
        },
        colors: ['#4361ee'],
        tooltip: {
            x: {
                show: false,
            }
        }
    };

    // Simple chart for Unique Visitors
    var uniqueVisitsChart = {
        chart: {
            type: 'line',
            height: 200,
            id: 'unique-visits-chart'
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        series: [{
            name: 'Unique Visitors',
            data: [45, 52, 38, 24, 33, 26, 21, 20, 6, 8, 15, 10]
        }],
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        yaxis: {
            min: 0,
            labels: {
                minWidth: 30
            }
        },
        colors: ['#4361ee'],
        tooltip: {
            x: {
                show: false,
            }
        }
    };

    // Simple chart for Followers
    var followersChart = {
        chart: {
            type: 'line',
            height: 200,
            id: 'followers-chart'
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        series: [{
            name: 'Followers',
            data: [28, 29, 33, 36, 32, 32, 33]
        }],
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        yaxis: {
            min: 0,
            labels: {
                minWidth: 30
            }
        },
        colors: ['#8dbf42'],
        tooltip: {
            x: {
                show: false,
            }
        }
    };

    var referralChart = {
        chart: {
            type: 'line',
            height: 200,
            id: 'referral-chart'
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        series: [{
            name: 'Referral',
            data: [12, 15, 18, 22, 19, 25, 28]
        }],
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        yaxis: {
            min: 0,
            labels: {
                minWidth: 30
            }
        },
        colors: ['#e7515a'],
        tooltip: {
            x: {
                show: false,
            }
        }
    };

    // Simple chart for Engagement Rate
    var engagementChart = {
        chart: {
            type: 'line',
            height: 200,
            id: 'engagement-chart'
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        series: [{
            name: 'Engagement',
            data: [85, 88, 92, 89, 91, 94, 96]
        }],
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        yaxis: {
            min: 0,
            labels: {
                minWidth: 30
            }
        },
        colors: ['#f0932b'],
        tooltip: {
            x: {
                show: false,
            }
        }
    };

    // Render charts when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Total Visits Chart
        if (document.querySelector("#total-users")) {
            var totalUsersChart = new ApexCharts(document.querySelector("#total-users"), spark1);
            totalUsersChart.render();
        }

        // Paid Visits Chart
        if (document.querySelector("#paid-visits")) {
            var paidVisitsChart = new ApexCharts(document.querySelector("#paid-visits"), spark2);
            paidVisitsChart.render();
        }

        // Unique Visitors Chart
        if (document.querySelector("#uniqueVisits")) {
            var uniqueVisits = new ApexCharts(document.querySelector("#uniqueVisits"), uniqueVisitsChart);
            uniqueVisits.render();
        }

        // Followers Chart
        if (document.querySelector("#hybrid_followers")) {
            var followers = new ApexCharts(document.querySelector("#hybrid_followers"), followersChart);
            followers.render();
        }

        // Referral Chart
        if (document.querySelector("#hybrid_followers1")) {
            var referral = new ApexCharts(document.querySelector("#hybrid_followers1"), referralChart);
            referral.render();
        }

        // Engagement Rate Chart
        if (document.querySelector("#hybrid_followers3")) {
            var engagement = new ApexCharts(document.querySelector("#hybrid_followers3"), engagementChart);
            engagement.render();
        }
    });

} catch(e) {
    console.log('Chart Error:', e);
}
