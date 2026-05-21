const ROOT_MARGIN = '100px 0px';
const IO_THRESHOLD = 0.08;

let chartApi = null;
/** @type {Map<string, import('chart.js').Chart>} */
const chartInstances = new Map();
/** @type {Set<string>} */
const renderedCanvasIds = new Set();

async function loadChartJs() {
    if (chartApi) {
        return chartApi;
    }

    const mod = await import('chart.js');

    mod.Chart.register(
        mod.CategoryScale,
        mod.LinearScale,
        mod.PointElement,
        mod.LineElement,
        mod.BarElement,
        mod.ArcElement,
        mod.BarController,
        mod.LineController,
        mod.DoughnutController,
        mod.Title,
        mod.Tooltip,
        mod.Legend,
        mod.Filler,
    );

    chartApi = mod;

    return chartApi;
}

function isDarkMode() {
    return document.documentElement.classList.contains('dark');
}

function themeColors() {
    const dark = isDarkMode();

    return {
        text: dark ? '#e2e8f0' : '#334155',
        grid: dark ? 'rgba(148, 163, 184, 0.15)' : 'rgba(148, 163, 184, 0.35)',
        income: dark ? '#34d399' : '#059669',
        incomeFill: dark ? 'rgba(52, 211, 153, 0.15)' : 'rgba(5, 150, 105, 0.12)',
        expense: dark ? '#fb7185' : '#e11d48',
        expenseFill: dark ? 'rgba(251, 113, 133, 0.15)' : 'rgba(225, 29, 72, 0.12)',
        net: dark ? '#818cf8' : '#4f46e5',
        netFill: dark ? 'rgba(129, 140, 248, 0.12)' : 'rgba(79, 70, 229, 0.1)',
    };
}

function baseOptions() {
    const colors = themeColors();

    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: colors.text,
                    usePointStyle: true,
                    padding: 16,
                },
            },
            tooltip: {
                backgroundColor: isDarkMode() ? '#1e293b' : '#fff',
                titleColor: colors.text,
                bodyColor: colors.text,
                borderColor: isDarkMode() ? '#334155' : '#e2e8f0',
                borderWidth: 1,
                padding: 12,
            },
        },
        scales: {
            x: {
                ticks: { color: colors.text },
                grid: { color: colors.grid },
            },
            y: {
                ticks: { color: colors.text },
                grid: { color: colors.grid },
                beginAtZero: true,
            },
        },
    };
}

function doughnutOptions() {
    const colors = themeColors();

    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    color: colors.text,
                    usePointStyle: true,
                    padding: 12,
                },
            },
            tooltip: baseOptions().plugins.tooltip,
        },
    };
}

function destroyChart(id) {
    const chart = chartInstances.get(id);
    if (chart) {
        chart.destroy();
        chartInstances.delete(id);
    }
}

function renderChart(id, data, Chart) {
    destroyChart(id);

    const colors = themeColors();
    const ctx = document.getElementById(id);
    if (!ctx) {
        return;
    }

    let chart;

    switch (id) {
        case 'chartMonthlyTrend':
            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.monthly.labels,
                    datasets: [
                        {
                            label: 'Income',
                            data: data.monthly.income,
                            borderColor: colors.income,
                            backgroundColor: colors.incomeFill,
                            fill: true,
                            tension: 0.35,
                        },
                        {
                            label: 'Expenses',
                            data: data.monthly.expense,
                            borderColor: colors.expense,
                            backgroundColor: colors.expenseFill,
                            fill: true,
                            tension: 0.35,
                        },
                        {
                            label: 'Net',
                            data: data.monthly.net,
                            borderColor: colors.net,
                            backgroundColor: colors.netFill,
                            fill: false,
                            tension: 0.35,
                            borderDash: [6, 4],
                        },
                    ],
                },
                options: baseOptions(),
            });
            break;
        case 'chartMonthlyBars': {
            const opts = baseOptions();
            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.monthly.labels,
                    datasets: [
                        {
                            label: 'Income',
                            data: data.monthly.income,
                            backgroundColor: colors.income,
                            borderRadius: 6,
                        },
                        {
                            label: 'Expenses',
                            data: data.monthly.expense,
                            backgroundColor: colors.expense,
                            borderRadius: 6,
                        },
                    ],
                },
                options: {
                    ...opts,
                    scales: {
                        ...opts.scales,
                        x: { ...opts.scales.x, stacked: false },
                        y: { ...opts.scales.y, stacked: false },
                    },
                },
            });
            break;
        }
        case 'chartIncomeExpense':
            chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.totals.labels,
                    datasets: [
                        {
                            data: data.totals.values,
                            backgroundColor: [colors.income, colors.expense],
                            borderWidth: 0,
                        },
                    ],
                },
                options: doughnutOptions(),
            });
            break;
        case 'chartExpenseCategories':
            if (!data.expenseByCategory.labels.length) {
                return;
            }
            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.expenseByCategory.labels,
                    datasets: [
                        {
                            label: 'Expenses',
                            data: data.expenseByCategory.values,
                            backgroundColor: data.expenseByCategory.colors,
                            borderRadius: 6,
                        },
                    ],
                },
                options: {
                    ...baseOptions(),
                    indexAxis: 'y',
                    plugins: {
                        ...baseOptions().plugins,
                        legend: { display: false },
                    },
                },
            });
            break;
        case 'chartIncomeCategories':
            if (!data.incomeByCategory.labels.length) {
                return;
            }
            chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.incomeByCategory.labels,
                    datasets: [
                        {
                            data: data.incomeByCategory.values,
                            backgroundColor: data.incomeByCategory.colors,
                            borderWidth: 0,
                        },
                    ],
                },
                options: doughnutOptions(),
            });
            break;
        default:
            return;
    }

    chartInstances.set(id, chart);
    renderedCanvasIds.add(id);
}

async function paintChart(id, data) {
    const { Chart } = await loadChartJs();
    renderChart(id, data, Chart);
}

async function repaintRendered(data) {
    if (!chartApi || renderedCanvasIds.size === 0) {
        return;
    }

    const { Chart } = chartApi;
    for (const id of renderedCanvasIds) {
        renderChart(id, data, Chart);
    }
}

export function initAnalysisCharts(data) {
    const root = document.querySelector('[data-analysis-root]');
    if (!root || !data) {
        return;
    }

    const wraps = root.querySelectorAll('.analysis-chart-wrap');
    if (!wraps.length) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                const canvas = entry.target.querySelector('canvas');
                if (!canvas?.id) {
                    return;
                }

                observer.unobserve(entry.target);
                paintChart(canvas.id, data);
            });
        },
        { rootMargin: ROOT_MARGIN, threshold: IO_THRESHOLD },
    );

    wraps.forEach((wrap) => {
        if (wrap.querySelector('canvas')) {
            observer.observe(wrap);
        }
    });

    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            setTimeout(() => repaintRendered(data), 50);
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('analysis-chart-data');
    if (!el) {
        return;
    }

    try {
        const data = JSON.parse(el.textContent);
        initAnalysisCharts(data);
    } catch (e) {
        console.error('Failed to load analysis charts', e);
    }
});
