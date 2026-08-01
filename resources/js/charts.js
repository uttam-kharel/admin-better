import Chart from 'chart.js/auto';

// Theme tokens from resources/css/theme/index.css
const COLORS = {
    primary: '#0F6CBD',
    secondary: '#14B8A6',
    grid: 'rgba(148, 163, 184, 0.18)',
    tick: '#475569',
    surface: '#FFFFFF',
    palette: ['#0F6CBD', '#14B8A6', '#EAB308', '#DC2626', '#16A34A', '#8B5CF6', '#F97316', '#0EA5E9'],
};

const charts = new Map();

export function destroyChart(el) {
    const chart = charts.get(el);
    if (chart) {
        chart.destroy();
        charts.delete(el);
    }
}

function baseOptions(extra = {}) {
    return {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 600, easing: 'easeOutQuart' },
        plugins: {
            legend: {
                display: false,
            },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.92)',
                padding: 10,
                cornerRadius: 8,
                titleColor: '#F8FAFC',
                bodyColor: '#CBD5E1',
                displayColors: false,
            },
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { color: COLORS.tick, maxRotation: 0, autoSkip: true, maxTicksLimit: 12, font: { size: 10 } },
            },
            y: {
                beginAtZero: true,
                grid: { color: COLORS.grid },
                border: { display: false },
                ticks: { color: COLORS.tick, precision: 0, font: { size: 10 } },
            },
        },
        ...extra,
    };
}

export function renderLineChart(el, labels, data, { label = 'Visits' } = {}) {
    destroyChart(el);
    charts.set(el, new Chart(el, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label,
                data,
                borderColor: COLORS.primary,
                backgroundColor: 'rgba(15, 108, 189, 0.12)',
                fill: true,
                tension: 0.35,
                borderWidth: 2.5,
                pointRadius: 3,
                pointHoverRadius: 5,
                pointBackgroundColor: COLORS.surface,
                pointBorderColor: COLORS.primary,
                pointBorderWidth: 2,
            }],
        },
        options: baseOptions(),
    }));
}

export function renderBarChart(el, labels, data, { label = 'Visits', horizontal = false, color = COLORS.primary } = {}) {
    destroyChart(el);
    charts.set(el, new Chart(el, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label,
                data,
                backgroundColor: color,
                hoverBackgroundColor: COLORS.primary,
                borderRadius: 4,
                maxBarThickness: 42,
            }],
        },
        options: baseOptions({
            indexAxis: horizontal ? 'y' : 'x',
            scales: horizontal
                ? {
                    x: {
                        beginAtZero: true,
                        grid: { color: COLORS.grid },
                        border: { display: false },
                        ticks: { color: COLORS.tick, precision: 0, font: { size: 10 } },
                    },
                    y: {
                        grid: { display: false },
                        ticks: { color: COLORS.tick, font: { size: 11 } },
                    },
                }
                : undefined,
        }),
    }));
}

export function renderDoughnut(el, labels, data, { colors = COLORS.palette } = {}) {
    destroyChart(el);
    charts.set(el, new Chart(el, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: colors.slice(0, Math.min(labels.length, colors.length)),
                borderColor: COLORS.surface,
                borderWidth: 3,
                hoverOffset: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '62%',
            animation: { duration: 600, easing: 'easeOutQuart' },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: COLORS.tick,
                        boxWidth: 10,
                        boxHeight: 10,
                        usePointStyle: true,
                        padding: 14,
                        font: { size: 11 },
                    },
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.92)',
                    padding: 10,
                    cornerRadius: 8,
                    titleColor: '#F8FAFC',
                    bodyColor: '#CBD5E1',
                },
            },
        },
    }));
}

window.AdminCharts = { renderLineChart, renderBarChart, renderDoughnut, destroyChart };
