import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const chartConfig = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                font: {
                    family: 'Inter, sans-serif',
                    size: 12,
                    weight: '500',
                },
                color: 'rgb(100, 116, 139)',
                usePointStyle: true,
                padding: 20,
            },
        },
        tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.95)',
            titleFont: {
                family: 'Inter, sans-serif',
                size: 12,
                weight: '600',
            },
            bodyFont: {
                family: 'Inter, sans-serif',
                size: 11,
            },
            padding: 12,
            borderRadius: 8,
        },
    },
    scales: {
        x: {
            grid: {
                display: false,
                drawBorder: false,
            },
            ticks: {
                font: {
                    family: 'Inter, sans-serif',
                    size: 11,
                    weight: '500',
                },
                color: 'rgb(100, 116, 139)',
            },
        },
        y: {
            grid: {
                color: 'rgba(203, 213, 225, 0.1)',
                drawBorder: false,
            },
            ticks: {
                font: {
                    family: 'Inter, sans-serif',
                    size: 11,
                    weight: '500',
                },
                color: 'rgb(100, 116, 139)',
            },
        },
    },
};

/**
 * Initialize 7-day task completion chart
 */
export function initTaskChart(data) {
    const ctx = document.getElementById('taskChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.label),
            datasets: [{
                label: 'Tasks Completed',
                data: data.map(d => d.value),
                borderColor: 'rgb(79, 70, 229)',
                backgroundColor: 'rgba(79, 70, 229, 0.05)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: 'rgb(79, 70, 229)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 6,
            }],
        },
        options: {
            ...chartConfig,
            plugins: {
                ...chartConfig.plugins,
                filler: true,
            },
        },
    });
}

/**
 * Initialize 7-day expense trend chart
 */
export function initExpenseChart(data) {
    const ctx = document.getElementById('expenseChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.label),
            datasets: [{
                label: 'Daily Expenses ($)',
                data: data.map(d => d.value),
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 0,
                borderRadius: 6,
                hoverBackgroundColor: 'rgb(34, 197, 94)',
            }],
        },
        options: {
            ...chartConfig,
            indexAxis: 'x',
        },
    });
}

/**
 * Initialize 7-day mood trend chart
 */
export function initMoodChart(data) {
    const ctx = document.getElementById('moodChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.label),
            datasets: [{
                label: 'Mood Level (1-10)',
                data: data.map(d => d.value),
                borderColor: 'rgb(249, 115, 22)',
                backgroundColor: 'rgba(249, 115, 22, 0.05)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: 'rgb(249, 115, 22)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 6,
                min: 0,
                max: 10,
            }],
        },
        options: {
            ...chartConfig,
            scales: {
                ...chartConfig.scales,
                y: {
                    ...chartConfig.scales.y,
                    min: 0,
                    max: 10,
                    beginAtZero: true,
                },
            },
        },
    });
}

/**
 * Initialize expense by category doughnut chart
 */
export function initCategoryChart(data) {
    const ctx = document.getElementById('categoryChart');
    if (!ctx) return;

    const colors = [
        'rgb(79, 70, 229)',
        'rgb(34, 197, 94)',
        'rgb(249, 115, 22)',
        'rgb(236, 72, 153)',
        'rgb(59, 130, 246)',
        'rgb(168, 85, 247)',
    ];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(d => d.label),
            datasets: [{
                data: data.map(d => d.value),
                backgroundColor: colors.slice(0, data.length),
                borderColor: '#fff',
                borderWidth: 2,
                hoverBorderColor: 'rgba(15, 23, 42, 0.1)',
            }],
        },
        options: {
            ...chartConfig,
            plugins: {
                ...chartConfig.plugins,
                legend: {
                    ...chartConfig.plugins.legend,
                    position: 'right',
                },
            },
        },
    });
}

/**
 * Initialize all dashboard charts
 */
export function initAllCharts(statsData) {
    if (statsData?.charts) {
        if (statsData.charts.tasks_7day?.length) {
            initTaskChart(statsData.charts.tasks_7day);
        }
        if (statsData.charts.expenses_7day?.length) {
            initExpenseChart(statsData.charts.expenses_7day);
        }
        if (statsData.charts.mood_7day?.length) {
            initMoodChart(statsData.charts.mood_7day);
        }
        if (statsData.charts.expenses_by_category?.length) {
            initCategoryChart(statsData.charts.expenses_by_category);
        }
    }
}

/**
 * Initialize weekly mood tracker chart
 */
export function initMoodTrackerWeeklyChart(data) {
    const ctx = document.getElementById('moodTrackerWeeklyChart');
    if (!ctx || !data?.length) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.label),
            datasets: [{
                label: 'Mood (1-10)',
                data: data.map(d => d.value),
                borderColor: 'rgb(14, 165, 233)',
                backgroundColor: 'rgba(14, 165, 233, 0.08)',
                borderWidth: 2,
                fill: true,
                tension: 0.35,
                pointRadius: 4,
                pointBackgroundColor: 'rgb(14, 165, 233)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 6,
                spanGaps: true,
            }],
        },
        options: {
            ...chartConfig,
            scales: {
                ...chartConfig.scales,
                y: {
                    ...chartConfig.scales.y,
                    min: 1,
                    max: 10,
                    ticks: {
                        ...chartConfig.scales.y.ticks,
                        stepSize: 1,
                    },
                },
            },
        },
    });
}
