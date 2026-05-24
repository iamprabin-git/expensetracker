<?php

namespace App\Services;

use App\Enums\CategoryType;
use App\Enums\TransactionType;
use App\Models\BudgetPlan;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class BudgetPlannerService
{
    public function resolveMonth(?string $monthInput): Carbon
    {
        if ($monthInput) {
            try {
                return Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
            } catch (\Throwable) {
                // fall through
            }
        }

        return now()->startOfMonth();
    }

    public function planForMonth(User $user, Carbon $month): BudgetPlan
    {
        return BudgetPlan::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'period_month' => $month->toDateString(),
            ],
            [
                'name' => $month->format('F Y').' Budget',
                'income_goal' => 0,
                'expense_limit' => 0,
            ],
        );
    }

    /**
     * @return array{
     *     month: Carbon,
     *     plan: BudgetPlan,
     *     summary: array<string, float|int>,
     *     expense_lines: Collection<int, array<string, mixed>>,
     *     income_lines: Collection<int, array<string, mixed>>,
     *     unbudgeted_expense: Collection<int, array<string, mixed>>,
     *     unbudgeted_income: Collection<int, array<string, mixed>>,
     *     categories: Collection<int, Category>,
     * }
     */
    public function build(User $user, Carbon $month): array
    {
        $plan = $this->planForMonth($user, $month);
        $plan->load(['items.category']);

        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $transactions = Transaction::query()
            ->where('user_id', $user->id)
            ->whereBetween('transaction_date', [$start, $end])
            ->get();

        $actualIncome = (float) $transactions->where('type', TransactionType::Income)->sum('amount');
        $actualExpense = (float) $transactions->where('type', TransactionType::Expense)->sum('amount');

        $budgetedExpense = (float) $plan->items
            ->filter(fn ($item) => $this->categoryMatchesBudgetSide($item->category?->type, TransactionType::Expense))
            ->sum('amount');

        $budgetedIncome = (float) $plan->items
            ->filter(fn ($item) => $this->categoryMatchesBudgetSide($item->category?->type, TransactionType::Income))
            ->sum('amount');

        $expenseLimit = (float) $plan->expense_limit;
        $incomeGoal = (float) $plan->income_goal;

        $categories = Category::forUser($user)->orderBy('name')->get();

        $expenseLines = $this->buildLines($plan, $transactions, TransactionType::Expense);
        $incomeLines = $this->buildLines($plan, $transactions, TransactionType::Income);
        $unbudgetedExpense = $this->unbudgetedActuals($plan, $transactions, TransactionType::Expense, $categories);
        $unbudgetedIncome = $this->unbudgetedActuals($plan, $transactions, TransactionType::Income, $categories);

        return [
            'month' => $month,
            'plan' => $plan,
            'summary' => [
                'income_goal' => $incomeGoal,
                'income_actual' => $actualIncome,
                'income_budgeted_categories' => $budgetedIncome,
                'income_remaining' => max($incomeGoal - $actualIncome, 0),
                'income_variance' => $actualIncome - $incomeGoal,
                'expense_limit' => $expenseLimit,
                'expense_actual' => $actualExpense,
                'expense_budgeted_categories' => $budgetedExpense,
                'expense_remaining' => max($expenseLimit - $actualExpense, 0),
                'expense_variance' => $actualExpense - $expenseLimit,
                'net_actual' => $actualIncome - $actualExpense,
                'net_planned' => $incomeGoal - $expenseLimit,
                'expense_percent' => $expenseLimit > 0 ? min(($actualExpense / $expenseLimit) * 100, 999) : 0,
                'income_percent' => $incomeGoal > 0 ? min(($actualIncome / $incomeGoal) * 100, 999) : 0,
            ],
            'expense_lines' => $expenseLines,
            'income_lines' => $incomeLines,
            'unbudgeted_expense' => $unbudgetedExpense,
            'unbudgeted_income' => $unbudgetedIncome,
            'categories' => $categories,
        ];
    }

    private function categoryMatchesBudgetSide(?CategoryType $categoryType, TransactionType $side): bool
    {
        if (! $categoryType) {
            return false;
        }

        return match ($side) {
            TransactionType::Income => $categoryType === CategoryType::Income,
            TransactionType::Expense => $categoryType === CategoryType::Expense,
        };
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function unbudgetedActuals(
        BudgetPlan $plan,
        Collection $transactions,
        TransactionType $type,
        Collection $categories,
    ): Collection {
        $budgetedIds = $plan->items->pluck('category_id');

        return $categories
            ->filter(fn (Category $category) => $this->categoryMatchesBudgetSide($category->type, $type))
            ->reject(fn (Category $category) => $budgetedIds->contains($category->id))
            ->map(function (Category $category) use ($transactions, $type) {
                $actual = (float) $transactions
                    ->where('category_id', $category->id)
                    ->where('type', $type)
                    ->sum('amount');

                return [
                    'category' => $category,
                    'actual' => $actual,
                ];
            })
            ->filter(fn (array $row) => $row['actual'] > 0)
            ->sortByDesc('actual')
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function buildLines(BudgetPlan $plan, Collection $transactions, TransactionType $type): Collection
    {
        $items = $plan->items->filter(fn ($item) => $this->categoryMatchesBudgetSide($item->category?->type, $type));

        return $items->map(function ($item) use ($transactions, $type) {
            $actual = (float) $transactions
                ->where('category_id', $item->category_id)
                ->where('type', $type)
                ->sum('amount');

            $budget = (float) $item->amount;
            $percent = $budget > 0 ? min(($actual / $budget) * 100, 999) : ($actual > 0 ? 100 : 0);
            $remaining = $budget - $actual;

            return [
                'item' => $item,
                'category' => $item->category,
                'budget' => $budget,
                'actual' => $actual,
                'remaining' => $remaining,
                'percent' => round($percent, 1),
                'over_budget' => $actual > $budget && $budget > 0,
                'status' => $this->statusFor($percent, $budget, $actual),
            ];
        })->sortByDesc('actual')->values();
    }

    private function statusFor(float $percent, float $budget, float $actual): string
    {
        if ($budget <= 0 && $actual <= 0) {
            return 'none';
        }

        if ($budget <= 0 && $actual > 0) {
            return 'unbudgeted';
        }

        if ($percent >= 100) {
            return 'over';
        }

        if ($percent >= 80) {
            return 'warning';
        }

        return 'ok';
    }

    public function duplicateFromPreviousMonth(User $user, Carbon $targetMonth): ?BudgetPlan
    {
        $source = BudgetPlan::query()
            ->where('user_id', $user->id)
            ->where('period_month', '<', $targetMonth->toDateString())
            ->orderByDesc('period_month')
            ->with('items')
            ->first();

        if (! $source) {
            return null;
        }

        $target = $this->planForMonth($user, $targetMonth);

        $target->update([
            'name' => $targetMonth->format('F Y').' Budget',
            'income_goal' => $source->income_goal,
            'expense_limit' => $source->expense_limit,
            'notes' => $source->notes,
        ]);

        $target->items()->delete();

        foreach ($source->items as $item) {
            $target->items()->create([
                'category_id' => $item->category_id,
                'amount' => $item->amount,
            ]);
        }

        return $target->fresh(['items.category']);
    }

    public function previousPlanLabel(User $user, Carbon $targetMonth): ?string
    {
        $source = BudgetPlan::query()
            ->where('user_id', $user->id)
            ->where('period_month', '<', $targetMonth->toDateString())
            ->orderByDesc('period_month')
            ->first();

        return $source?->period_month->format('F Y');
    }

    /**
     * Budget vs actual data for the analysis dashboard.
     *
     * @return array{
     *     month: string,
     *     month_label: string,
     *     has_plan: bool,
     *     summary: array<string, float|int>,
     *     expense_chart: array{labels: list<string>, budget: list<float>, actual: list<float>},
     *     income_chart: array{labels: list<string>, budget: list<float>, actual: list<float>},
     *     expense_lines: list<array<string, mixed>>,
     *     income_lines: list<array<string, mixed>>,
     *     over_budget_count: int,
     * }
     */
    public function analysisPayload(User $user, Carbon $month): array
    {
        $built = $this->build($user, $month);
        $summary = $built['summary'];
        $plan = $built['plan'];

        $expenseLines = $built['expense_lines'];
        $incomeLines = $built['income_lines'];

        return [
            'month' => $month->format('Y-m'),
            'month_label' => $month->format('F Y'),
            'has_plan' => $plan->items->isNotEmpty()
                || $summary['expense_limit'] > 0
                || $summary['income_goal'] > 0,
            'summary' => $summary,
            'expense_chart' => $this->linesToChart($expenseLines),
            'income_chart' => $this->linesToChart($incomeLines),
            'expense_lines' => $this->linesForTable($expenseLines),
            'income_lines' => $this->linesForTable($incomeLines),
            'over_budget_count' => $expenseLines->where('over_budget', true)->count(),
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $lines
     * @return array{labels: list<string>, budget: list<float>, actual: list<float>}
     */
    private function linesToChart(Collection $lines): array
    {
        if ($lines->isEmpty()) {
            return ['labels' => [], 'budget' => [], 'actual' => []];
        }

        return [
            'labels' => $lines->map(fn (array $line) => $line['category']?->name ?? 'Category')->all(),
            'budget' => $lines->map(fn (array $line) => round((float) $line['budget'], 2))->all(),
            'actual' => $lines->map(fn (array $line) => round((float) $line['actual'], 2))->all(),
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $lines
     * @return list<array<string, mixed>>
     */
    private function linesForTable(Collection $lines): array
    {
        return $lines->map(function (array $line) {
            return [
                'category_id' => $line['category']?->id,
                'category_name' => $line['category']?->name ?? 'Category',
                'category_icon' => $line['category']?->resolvedIcon(),
                'budget' => (float) $line['budget'],
                'actual' => (float) $line['actual'],
                'remaining' => (float) $line['remaining'],
                'percent' => (float) $line['percent'],
                'over_budget' => (bool) $line['over_budget'],
                'status' => $line['status'],
            ];
        })->all();
    }
}
