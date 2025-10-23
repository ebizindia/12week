var GoalProgress = {
    weeklyData:null,
    trendsData:null,

    init:()=>{
        // Chart.js global configuration
        Chart.defaults.font.family = "'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif";
        Chart.defaults.color = '#6c757d';
        if(GoalProgress.weeklyData)
            GoalProgress.genWeeklyChart();
        if(GoalProgress.trendsData)
            GoalProgress.genTrendsChart();
    },
    genWeeklyChart:()=>{
        weeklyCtx = document.getElementById('weeklyProgressChart');

        new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: GoalProgress.weeklyData.map(w => 'Week ' + w.week_number),
                datasets: [{
                    label: 'Weekly Score (%)',
                    data: GoalProgress.weeklyData.map(w => w.avg_score),
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(102, 126, 234)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return 'Score: ' + context.parsed.y.toFixed(1) + '%';
                            }
                        }
                    }
                }
            }
        });
    },
    genTrendsChart:()=>{
        trendsCtx = document.getElementById('completionTrendsChart');

        new Chart(trendsCtx, {
            type: 'bar',
            data: {
                labels: GoalProgress.trendsData.map(t => 'Week ' + t.week_number),
                datasets: [
                    {
                        label: 'Completed',
                        data: GoalProgress.trendsData.map(t => t.completed),
                        backgroundColor: 'rgba(17, 153, 142, 0.8)',
                        borderRadius: 6
                    },
                    {
                        label: 'In Progress',
                        data: GoalProgress.trendsData.map(t => t.in_progress),
                        backgroundColor: 'rgba(242, 153, 74, 0.8)',
                        borderRadius: 6
                    },
                    {
                        label: 'Not Started',
                        data: GoalProgress.trendsData.map(t => t.not_started),
                        backgroundColor: 'rgba(238, 9, 121, 0.8)',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    x: {
                        stacked: false,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8
                    }
                }
            }
        });
    }

}

function changeCycle(cycleId) {
    if (cycleId) {
        window.location.href = '12-week-progress.php?cycle_id=' + cycleId;
    } else {
        window.location.href = '12-week-progress.php';
    }
}