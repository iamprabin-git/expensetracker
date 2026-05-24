<x-user-layout>
    <x-slot name="header">Analysis</x-slot>
    <x-slot name="subheader">Power BI–style insights for your income and expenses.</x-slot>

    @push('styles')
        @vite(['resources/css/analysis.css'])
    @endpush
    @push('scripts')
        @vite(['resources/js/analysis.js'])
    @endpush

    <div data-analysis-root>
        <div class="grid grid-cols-12 gap-3 md:gap-4 mb-4">
            <div class="col-span-12 sm:col-span-6 xl:col-span-3">
                <div class="analysis-kpi card-panel h-full">
                    <p class="analysis-kpi__label">Total income</p>
                    <p class="analysis-kpi__value text-emerald-600 dark:text-emerald-400"><x-money :amount="$summary['income']" /></p>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-6 xl:col-span-3">
                <div class="analysis-kpi card-panel h-full">
                    <p class="analysis-kpi__label">Total expenses</p>
                    <p class="analysis-kpi__value text-rose-600 dark:text-rose-400"><x-money :amount="$summary['expense']" /></p>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-6 xl:col-span-3">
                <div class="analysis-kpi card-panel h-full">
                    <p class="analysis-kpi__label">Net balance</p>
                    <p @class([
                        'analysis-kpi__value',
                        $summary['balance'] >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-rose-600 dark:text-rose-400',
                    ])><x-money :amount="$summary['balance']" /></p>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-6 xl:col-span-3">
                <div class="analysis-kpi card-panel h-full">
                    <p class="analysis-kpi__label">This month (net)</p>
                    <p @class([
                        'analysis-kpi__value',
                        $summary['monthly_balance'] >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-rose-600 dark:text-rose-400',
                    ])><x-money :amount="$summary['monthly_balance']" /></p>
                    <p class="analysis-kpi__meta small text-muted-foreground mb-0">
                        <x-money :amount="$summary['monthly_income']" /> in · <x-money :amount="$summary['monthly_expense']" /> out
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 col-xl-8">
                <div class="card-panel analysis-chart-card h-full">
                    <h2 class="h6 font-semibold mb-1">Income vs expenses trend</h2>
                    <p class="text-sm text-muted-foreground mb-3">Last 12 months — line view</p>
                    <div class="analysis-chart-wrap">
                        <canvas id="chartMonthlyTrend" aria-label="Monthly income and expense trend"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-span-12 col-xl-4">
                <div class="card-panel analysis-chart-card h-full">
                    <h2 class="h6 font-semibold mb-1">Overall split</h2>
                    <p class="text-sm text-muted-foreground mb-3">All-time income vs expenses</p>
                    <div class="analysis-chart-wrap analysis-chart-wrap--donut">
                        <canvas id="chartIncomeExpense" aria-label="Income versus expenses"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-span-12">
                <div class="card-panel analysis-chart-card">
                    <h2 class="h6 font-semibold mb-1">Monthly comparison</h2>
                    <p class="text-sm text-muted-foreground mb-3">Side-by-side bars per month</p>
                    <div class="analysis-chart-wrap analysis-chart-wrap--tall">
                        <canvas id="chartMonthlyBars" aria-label="Monthly income and expense bars"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-span-12 lg:col-span-6">
                <div class="card-panel analysis-chart-card h-full">
                    <h2 class="h6 font-semibold mb-1">Expenses by category</h2>
                    <p class="text-sm text-muted-foreground mb-3">Where your money goes</p>
                    @if (count($chartData['expenseByCategory']['labels']) > 0)
                        <div class="analysis-chart-wrap analysis-chart-wrap--tall">
                            <canvas id="chartExpenseCategories" aria-label="Expenses by category"></canvas>
                        </div>
                    @else
                        <p class="text-muted-foreground small py-5 text-center mb-0">No expense data yet. Add transactions to see this chart.</p>
                    @endif
                </div>
            </div>
            <div class="col-span-12 lg:col-span-6">
                <div class="card-panel analysis-chart-card h-full">
                    <h2 class="h6 font-semibold mb-1">Income by category</h2>
                    <p class="text-sm text-muted-foreground mb-3">Sources of income</p>
                    @if (count($chartData['incomeByCategory']['labels']) > 0)
                        <div class="analysis-chart-wrap analysis-chart-wrap--donut">
                            <canvas id="chartIncomeCategories" aria-label="Income by category"></canvas>
                        </div>
                    @else
                        <p class="text-muted-foreground small py-5 text-center mb-0">No income data yet. Add transactions to see this chart.</p>
                    @endif
                </div>
            </div>
        </div>

        @php
            $budgetSummary = $budget['summary'];
            $user = auth()->user();
        @endphp

        <div class="mt-4 pt-2 border-t border-border">
            <div class="flex flex-col gap-3 mb-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold mb-1">Budget analysis</h2>
                    <p class="text-sm text-muted-foreground mb-0">Planned vs actual for {{ $budget['month_label'] }} — from your budget plan and transactions.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <form method="GET" action="{{ route('analysis.index') }}" class="flex items-center gap-2">
                        <label for="analysis-budget-month" class="text-sm text-muted-foreground mb-0">Month</label>
                        <input
                            type="month"
                            id="analysis-budget-month"
                            name="month"
                            value="{{ $budget['month'] }}"
                            class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs"
                            onchange="this.form.submit()"
                        >
                    </form>
                    <x-ui.button variant="outline" size="sm" href="{{ route('budgets.index', ['month' => $budget['month']]) }}">Edit budget</x-ui.button>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-3 md:gap-4 mb-4">
                <div class="col-span-12 sm:col-span-6 xl:col-span-3">
                    <div class="analysis-kpi card-panel h-full">
                        <p class="analysis-kpi__label">Expense limit</p>
                        <p class="analysis-kpi__value text-rose-600 dark:text-rose-400">{{ $user->formatMoney($budgetSummary['expense_limit']) }}</p>
                        <p class="analysis-kpi__meta small text-muted-foreground mb-0">
                            Spent {{ $user->formatMoney($budgetSummary['expense_actual']) }}
                            @if ($budgetSummary['expense_limit'] > 0)
                                ({{ round($budgetSummary['expense_percent'], 1) }}%)
                            @endif
                        </p>
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-6 xl:col-span-3">
                    <div class="analysis-kpi card-panel h-full">
                        <p class="analysis-kpi__label">Income goal</p>
                        <p class="analysis-kpi__value text-emerald-600 dark:text-emerald-400">{{ $user->formatMoney($budgetSummary['income_goal']) }}</p>
                        <p class="analysis-kpi__meta small text-muted-foreground mb-0">
                            Earned {{ $user->formatMoney($budgetSummary['income_actual']) }}
                            @if ($budgetSummary['income_goal'] > 0)
                                ({{ round($budgetSummary['income_percent'], 1) }}%)
                            @endif
                        </p>
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-6 xl:col-span-3">
                    <div class="analysis-kpi card-panel h-full">
                        <p class="analysis-kpi__label">Net (actual)</p>
                        <p @class([
                            'analysis-kpi__value',
                            $budgetSummary['net_actual'] >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-rose-600 dark:text-rose-400',
                        ])>{{ $user->formatMoney($budgetSummary['net_actual']) }}</p>
                        <p class="analysis-kpi__meta small text-muted-foreground mb-0">Planned {{ $user->formatMoney($budgetSummary['net_planned']) }}</p>
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-6 xl:col-span-3">
                    <div class="analysis-kpi card-panel h-full">
                        <p class="analysis-kpi__label">Budget headroom</p>
                        <p class="analysis-kpi__value">{{ $user->formatMoney($budgetSummary['expense_remaining']) }}</p>
                        <p class="analysis-kpi__meta small text-muted-foreground mb-0">
                            @if ($budget['over_budget_count'] > 0)
                                {{ $budget['over_budget_count'] }} {{ $budget['over_budget_count'] === 1 ? 'category' : 'categories' }} over budget
                            @else
                                No categories over budget
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 lg:col-span-7">
                    <div class="card-panel analysis-chart-card h-full">
                        <h3 class="h6 font-semibold mb-1">Expense: budget vs spent</h3>
                        <p class="text-sm text-muted-foreground mb-3">By category for {{ $budget['month_label'] }}</p>
                        @if (count($budget['expense_chart']['labels']) > 0)
                            <div class="analysis-chart-wrap analysis-chart-wrap--tall">
                                <canvas id="chartBudgetExpense" aria-label="Expense budget versus actual"></canvas>
                            </div>
                        @else
                            <p class="text-muted-foreground small py-5 text-center mb-0">
                                No expense category budgets yet.
                                <a href="{{ route('budgets.index', ['month' => $budget['month']]) }}" class="text-primary">Set up your budget</a>.
                            </p>
                        @endif
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-5">
                    <div class="card-panel analysis-chart-card h-full">
                        <h3 class="h6 font-semibold mb-1">Income: goal vs earned</h3>
                        <p class="text-sm text-muted-foreground mb-3">By category for {{ $budget['month_label'] }}</p>
                        @if (count($budget['income_chart']['labels']) > 0)
                            <div class="analysis-chart-wrap analysis-chart-wrap--tall">
                                <canvas id="chartBudgetIncome" aria-label="Income budget versus actual"></canvas>
                            </div>
                        @else
                            <p class="text-muted-foreground small py-5 text-center mb-0">No income category budgets for this month.</p>
                        @endif
                    </div>
                </div>

                @if (count($budget['expense_lines']) > 0)
                    <div class="col-span-12">
                        <div class="card-panel">
                            <h3 class="h6 font-semibold mb-3">Expense budget breakdown</h3>
                            <div class="overflow-x-auto table-scroll-touch">
                                <table class="w-full align-middle mb-0 table-mobile-stack text-sm">
                                    <thead>
                                        <tr class="text-muted-foreground border-b border-border">
                                            <th class="text-left py-2 font-medium">Category</th>
                                            <th class="text-right py-2 font-medium">Budget</th>
                                            <th class="text-right py-2 font-medium">Spent</th>
                                            <th class="text-right py-2 font-medium">Remaining</th>
                                            <th class="py-2 font-medium min-w-[8rem]">Progress</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($budget['expense_lines'] as $line)
                                            <tr class="border-b border-border/80">
                                                <td class="py-2.5" data-label="Category">
                                                    <span class="inline-flex items-center gap-2">
                                                        @if ($line['category_id'])
                                                            <x-category-icon :icon="$line['category_icon']" class="size-7 [&_svg]:size-3.5" />
                                                        @endif
                                                        {{ $line['category_name'] }}
                                                    </span>
                                                </td>
                                                <td class="text-right py-2.5 tabular-nums" data-label="Budget">{{ $user->formatMoney($line['budget']) }}</td>
                                                <td class="text-right py-2.5 tabular-nums" data-label="Spent">{{ $user->formatMoney($line['actual']) }}</td>
                                                <td @class([
                                                    'text-right py-2.5 tabular-nums',
                                                    'text-rose-600 dark:text-rose-400' => $line['remaining'] < 0,
                                                ]) data-label="Remaining">{{ $user->formatMoney($line['remaining']) }}</td>
                                                <td class="py-2.5" data-label="Progress">
                                                    @include('budgets._meter', [
                                                        'percent' => $line['percent'],
                                                        'status' => $line['status'],
                                                        'variant' => 'danger',
                                                        'hideLabel' => true,
                                                    ])
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script type="application/json" id="analysis-chart-data">@json(array_merge($chartData, ['budget' => $budget]))</script>
</x-user-layout>
