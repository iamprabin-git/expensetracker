<x-user-layout>
    <x-slot name="header">Analysis</x-slot>
    <x-slot name="subheader">Power BI–style insights for your income and expenses.</x-slot>

    @push('scripts')
        @vite(['resources/js/analysis.js'])
    @endpush

    <div data-analysis-root>
        <div class="row g-3 g-md-4 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="analysis-kpi card-panel h-100">
                    <p class="analysis-kpi__label">Total income</p>
                    <p class="analysis-kpi__value text-emerald-600 dark:text-emerald-400"><x-money :amount="$summary['income']" /></p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="analysis-kpi card-panel h-100">
                    <p class="analysis-kpi__label">Total expenses</p>
                    <p class="analysis-kpi__value text-rose-600 dark:text-rose-400"><x-money :amount="$summary['expense']" /></p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="analysis-kpi card-panel h-100">
                    <p class="analysis-kpi__label">Net balance</p>
                    <p @class([
                        'analysis-kpi__value',
                        $summary['balance'] >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-rose-600 dark:text-rose-400',
                    ])><x-money :amount="$summary['balance']" /></p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="analysis-kpi card-panel h-100">
                    <p class="analysis-kpi__label">This month (net)</p>
                    <p @class([
                        'analysis-kpi__value',
                        $summary['monthly_balance'] >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-rose-600 dark:text-rose-400',
                    ])><x-money :amount="$summary['monthly_balance']" /></p>
                    <p class="analysis-kpi__meta small text-secondary mb-0">
                        <x-money :amount="$summary['monthly_income']" /> in · <x-money :amount="$summary['monthly_expense']" /> out
                    </p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <div class="card-panel analysis-chart-card h-100">
                    <h2 class="h6 fw-semibold mb-1">Income vs expenses trend</h2>
                    <p class="small text-secondary mb-3">Last 12 months — line view</p>
                    <div class="analysis-chart-wrap">
                        <canvas id="chartMonthlyTrend" aria-label="Monthly income and expense trend"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="card-panel analysis-chart-card h-100">
                    <h2 class="h6 fw-semibold mb-1">Overall split</h2>
                    <p class="small text-secondary mb-3">All-time income vs expenses</p>
                    <div class="analysis-chart-wrap analysis-chart-wrap--donut">
                        <canvas id="chartIncomeExpense" aria-label="Income versus expenses"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card-panel analysis-chart-card">
                    <h2 class="h6 fw-semibold mb-1">Monthly comparison</h2>
                    <p class="small text-secondary mb-3">Side-by-side bars per month</p>
                    <div class="analysis-chart-wrap analysis-chart-wrap--tall">
                        <canvas id="chartMonthlyBars" aria-label="Monthly income and expense bars"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card-panel analysis-chart-card h-100">
                    <h2 class="h6 fw-semibold mb-1">Expenses by category</h2>
                    <p class="small text-secondary mb-3">Where your money goes</p>
                    @if (count($chartData['expenseByCategory']['labels']) > 0)
                        <div class="analysis-chart-wrap analysis-chart-wrap--tall">
                            <canvas id="chartExpenseCategories" aria-label="Expenses by category"></canvas>
                        </div>
                    @else
                        <p class="text-secondary small py-5 text-center mb-0">No expense data yet. Add transactions to see this chart.</p>
                    @endif
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card-panel analysis-chart-card h-100">
                    <h2 class="h6 fw-semibold mb-1">Income by category</h2>
                    <p class="small text-secondary mb-3">Sources of income</p>
                    @if (count($chartData['incomeByCategory']['labels']) > 0)
                        <div class="analysis-chart-wrap analysis-chart-wrap--donut">
                            <canvas id="chartIncomeCategories" aria-label="Income by category"></canvas>
                        </div>
                    @else
                        <p class="text-secondary small py-5 text-center mb-0">No income data yet. Add transactions to see this chart.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script type="application/json" id="analysis-chart-data">@json($chartData)</script>
</x-user-layout>
