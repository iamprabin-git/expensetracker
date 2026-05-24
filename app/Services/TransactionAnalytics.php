<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use App\Support\CategoryIcons;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TransactionAnalytics
{
    public function __construct(
        private readonly User $user,
        private readonly int $months = 12,
    ) {}

    public static function for(User $user, int $months = 12): self
    {
        return new self($user, $months);
    }

    public function summary(): array
    {
        $income = $this->baseQuery()->where('type', TransactionType::Income)->sum('amount');
        $expense = $this->baseQuery()->where('type', TransactionType::Expense)->sum('amount');

        $monthStart = now()->startOfMonth();

        $monthlyIncome = $this->baseQuery()
            ->where('type', TransactionType::Income)
            ->where('transaction_date', '>=', $monthStart)
            ->sum('amount');

        $monthlyExpense = $this->baseQuery()
            ->where('type', TransactionType::Expense)
            ->where('transaction_date', '>=', $monthStart)
            ->sum('amount');

        return [
            'income' => (float) $income,
            'expense' => (float) $expense,
            'balance' => (float) $income - (float) $expense,
            'monthly_income' => (float) $monthlyIncome,
            'monthly_expense' => (float) $monthlyExpense,
            'monthly_balance' => (float) $monthlyIncome - (float) $monthlyExpense,
        ];
    }

    public function monthlyTrend(): array
    {
        $period = $this->monthPeriod();
        $transactions = $this->baseQuery()
            ->where('transaction_date', '>=', $period->first()['start'])
            ->get(['type', 'amount', 'transaction_date']);

        $grouped = $transactions->groupBy(
            fn (Transaction $t) => $t->transaction_date->format('Y-m'),
        );

        $labels = [];
        $income = [];
        $expense = [];
        $net = [];

        foreach ($period as $month) {
            $key = $month['key'];
            $rows = $grouped->get($key, collect());

            $incomeSum = (float) $rows->where('type', TransactionType::Income)->sum('amount');
            $expenseSum = (float) $rows->where('type', TransactionType::Expense)->sum('amount');

            $labels[] = $month['label'];
            $income[] = round($incomeSum, 2);
            $expense[] = round($expenseSum, 2);
            $net[] = round($incomeSum - $expenseSum, 2);
        }

        return compact('labels', 'income', 'expense', 'net');
    }

    public function byCategory(TransactionType $type, int $limit = 8): array
    {
        $rows = Transaction::query()
            ->where('transactions.user_id', $this->user->id)
            ->where('transactions.type', $type)
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->selectRaw("COALESCE(categories.name, 'Uncategorized') as name")
            ->selectRaw('SUM(transactions.amount) as total')
            ->groupByRaw("COALESCE(categories.name, 'Uncategorized')")
            ->orderByDesc('total')
            ->limit($limit)
            ->get();

        return [
            'labels' => $rows->pluck('name')->all(),
            'values' => $rows->map(fn ($r) => round((float) $r->total, 2))->all(),
            'colors' => $rows->values()->map(
                fn ($row, int $index) => CategoryIcons::chartColor($index)
            )->all(),
        ];
    }

    public function incomeVsExpenseTotals(): array
    {
        $summary = $this->summary();

        return [
            'labels' => ['Income', 'Expenses'],
            'values' => [$summary['income'], $summary['expense']],
        ];
    }

    public function chartPayload(): array
    {
        return [
            'summary' => $this->summary(),
            'monthly' => $this->monthlyTrend(),
            'expenseByCategory' => $this->byCategory(TransactionType::Expense),
            'incomeByCategory' => $this->byCategory(TransactionType::Income),
            'totals' => $this->incomeVsExpenseTotals(),
        ];
    }

    private function baseQuery()
    {
        return Transaction::query()->where('user_id', $this->user->id);
    }

    /**
     * @return Collection<int, array{key: string, label: string, start: Carbon}>
     */
    private function monthPeriod(): Collection
    {
        $period = collect();

        for ($i = $this->months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i)->startOfMonth();
            $period->push([
                'key' => $date->format('Y-m'),
                'label' => $date->format('M Y'),
                'start' => $date,
            ]);
        }

        return $period;
    }
}
