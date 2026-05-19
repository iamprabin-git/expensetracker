import {
    ArcElement,
    BarController,
    BarElement,
    CategoryScale,
    Chart,
    DoughnutController,
    Filler,
    Legend,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Title,
    Tooltip,
} from 'chart.js';

Chart.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    BarController,
    LineController,
    DoughnutController,
    Title,
    Tooltip,
    Legend,
    Filler,
);

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

export function initAnalysisCharts(data) {
    const root = document.querySelector('[data-analysis-root]');
    if (!root || !data) {
        return;
    }

    const charts = [];

    const destroyAll = () => {
        charts.forEach((chart) => chart.destroy());
        charts.length = 0;
    };

    const render = () => {
        destroyAll();
        const colors = themeColors();

        const trendCtx = document.getElementById('chartMonthlyTrend');
        if (trendCtx) {
            charts.push(
                new Chart(trendCtx, {
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
                }),
            );
        }

        const barCtx = document.getElementById('chartMonthlyBars');
        if (barCtx) {
            charts.push(
                new Chart(barCtx, {
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
                        ...baseOptions(),
                        scales: {
                            ...baseOptions().scales,
                            x: { ...baseOptions().scales.x, stacked: false },
                            y: { ...baseOptions().scales.y, stacked: false },
                        },
                    },
                }),
            );
        }

        const totalsCtx = document.getElementById('chartIncomeExpense');
        if (totalsCtx) {
            charts.push(
                new Chart(totalsCtx, {
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
                }),
            );
        }

        const expenseCtx = document.getElementById('chartExpenseCategories');
        if (expenseCtx && data.expenseByCategory.labels.length) {
            charts.push(
                new Chart(expenseCtx, {
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
                }),
            );
        }

        const incomeCtx = document.getElementById('chartIncomeCategories');
        if (incomeCtx && data.incomeByCategory.labels.length) {
            charts.push(
                new Chart(incomeCtx, {
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
                }),
            );
        }
    };

    render();

    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            setTimeout(render, 50);
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
