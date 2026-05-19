<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class FinancialReportBuilder
{
    public function __construct(
        private readonly User $user,
        private readonly ?string $fromDate = null,
        private readonly ?string $toDate = null,
    ) {}

    public static function fromRequest(User $user, array $input): self
    {
        return new self(
            $user,
            $input['from_date'] ?? null,
            $input['to_date'] ?? null,
        );
    }

    public function periodLabel(): string
    {
        if ($this->fromDate && $this->toDate) {
            return Carbon::parse($this->fromDate)->format('M d, Y')
                .' — '
                .Carbon::parse($this->toDate)->format('M d, Y');
        }

        if ($this->fromDate) {
            return 'From '.Carbon::parse($this->fromDate)->format('M d, Y');
        }

        if ($this->toDate) {
            return 'As of '.Carbon::parse($this->toDate)->format('M d, Y');
        }

        return 'All periods';
    }

    public function asOfDate(): Carbon
    {
        return $this->toDate
            ? Carbon::parse($this->toDate)->endOfDay()
            : now();
    }

    public function filters(): array
    {
        return [
            'from_date' => $this->fromDate,
            'to_date' => $this->toDate,
        ];
    }

    /**
     * Transactions within the selected period.
     *
     * @return Collection<int, Transaction>
     */
    public function periodTransactions(): Collection
    {
        return $this->queryThrough($this->fromDate, $this->toDate)->get();
    }

    /**
     * All transactions up to and including as-of date.
     *
     * @return Collection<int, Transaction>
     */
    public function cumulativeTransactions(): Collection
    {
        $end = $this->asOfDate()->toDateString();

        return $this->queryThrough(null, $end)->get();
    }

    /**
     * Transactions before period start (for opening cash).
     *
     * @return Collection<int, Transaction>
     */
    public function transactionsBeforePeriod(): Collection
    {
        if (! $this->fromDate) {
            return collect();
        }

        $before = Carbon::parse($this->fromDate)->subDay()->toDateString();

        return $this->queryThrough(null, $before)->get();
    }

    public function trialBalance(): array
    {
        $transactions = $this->periodTransactions();
        $lines = [];

        $incomeByCategory = $this->sumByCategory($transactions, TransactionType::Income);
        foreach ($incomeByCategory as $row) {
            $lines[] = [
                'account' => $row['name'].' (Income)',
                'type' => 'income',
                'debit' => 0.0,
                'credit' => $row['total'],
            ];
        }

        $expenseByCategory = $this->sumByCategory($transactions, TransactionType::Expense);
        foreach ($expenseByCategory as $row) {
            $lines[] = [
                'account' => $row['name'].' (Expense)',
                'type' => 'expense',
                'debit' => $row['total'],
                'credit' => 0.0,
            ];
        }

        $net = $this->sumIncome($transactions) - $this->sumExpense($transactions);
        if ($net >= 0) {
            $lines[] = [
                'account' => 'Cash & equivalents (net increase)',
                'type' => 'cash',
                'debit' => $net,
                'credit' => 0.0,
            ];
        } elseif ($net < 0) {
            $lines[] = [
                'account' => 'Cash & equivalents (net decrease)',
                'type' => 'cash',
                'debit' => 0.0,
                'credit' => abs($net),
            ];
        }

        $totalDebit = collect($lines)->sum('debit');
        $totalCredit = collect($lines)->sum('credit');

        return [
            'lines' => $lines,
            'total_debit' => round($totalDebit, 2),
            'total_credit' => round($totalCredit, 2),
            'balanced' => abs($totalDebit - $totalCredit) < 0.01,
        ];
    }

    public function profitAndLoss(): array
    {
        $transactions = $this->periodTransactions();
        $revenue = $this->sumByCategory($transactions, TransactionType::Income);
        $expenses = $this->sumByCategory($transactions, TransactionType::Expense);

        $totalRevenue = $this->sumIncome($transactions);
        $totalExpenses = $this->sumExpense($transactions);
        $netProfit = $totalRevenue - $totalExpenses;

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'total_revenue' => round($totalRevenue, 2),
            'total_expenses' => round($totalExpenses, 2),
            'net_profit' => round($netProfit, 2),
            'is_profit' => $netProfit >= 0,
        ];
    }

    public function balanceSheet(): array
    {
        $cumulative = $this->cumulativeTransactions();
        $cash = $this->sumIncome($cumulative) - $this->sumExpense($cumulative);
        $cash = round(max($cash, 0), 2);

        $equity = round($this->sumIncome($cumulative) - $this->sumExpense($cumulative), 2);
        $liabilities = 0.0;
        $assets = round($cash, 2);

        return [
            'as_of' => $this->asOfDate()->format('M d, Y'),
            'assets' => [
                ['name' => 'Cash and cash equivalents', 'amount' => $cash],
            ],
            'liabilities' => [
                ['name' => 'Accounts payable', 'amount' => $liabilities],
            ],
            'equity' => [
                ['name' => 'Retained earnings (net worth)', 'amount' => $equity],
            ],
            'total_assets' => $assets,
            'total_liabilities' => $liabilities,
            'total_equity' => $equity,
            'total_liabilities_equity' => round($liabilities + $equity, 2),
            'balanced' => abs($assets - ($liabilities + $equity)) < 0.01,
        ];
    }

    public function cashFlow(): array
    {
        $period = $this->periodTransactions();
        $before = $this->transactionsBeforePeriod();

        $cashFromIncome = $this->sumIncome($period);
        $cashPaidExpenses = $this->sumExpense($period);
        $netOperating = $cashFromIncome - $cashPaidExpenses;

        $openingCash = $this->sumIncome($before) - $this->sumExpense($before);
        $closingCash = $openingCash + $netOperating;

        $monthly = $this->monthlyCashFlow($period);

        return [
            'operating' => [
                ['label' => 'Cash received from income', 'amount' => round($cashFromIncome, 2)],
                ['label' => 'Cash paid for expenses', 'amount' => round(-$cashPaidExpenses, 2)],
                ['label' => 'Net cash from operating activities', 'amount' => round($netOperating, 2), 'bold' => true],
            ],
            'investing' => [
                ['label' => 'Investing activities', 'amount' => 0.0],
                ['label' => 'Net cash from investing activities', 'amount' => 0.0, 'bold' => true],
            ],
            'financing' => [
                ['label' => 'Financing activities', 'amount' => 0.0],
                ['label' => 'Net cash from financing activities', 'amount' => 0.0, 'bold' => true],
            ],
            'summary' => [
                'net_change' => round($netOperating, 2),
                'opening_cash' => round($openingCash, 2),
                'closing_cash' => round($closingCash, 2),
            ],
            'monthly' => $monthly,
        ];
    }

    public function transactionStatement(): array
    {
        $transactions = $this->periodTransactions();
        $income = $this->sumIncome($transactions);
        $expense = $this->sumExpense($transactions);

        return [
            'transactions' => $transactions,
            'totals' => [
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense,
                'count' => $transactions->count(),
            ],
        ];
    }

    private function queryThrough(?string $from, ?string $to)
    {
        $query = Transaction::query()
            ->where('user_id', $this->user->id)
            ->with('category')
            ->orderBy('transaction_date')
            ->orderBy('id');

        if ($from) {
            $query->whereDate('transaction_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('transaction_date', '<=', $to);
        }

        return $query;
    }

    /**
     * @return Collection<int, Transaction>
     */
    private function sumByCategory(Collection $transactions, TransactionType $type): Collection
    {
        return $transactions
            ->where('type', $type)
            ->groupBy(fn (Transaction $t) => $t->category?->name ?? 'Uncategorized')
            ->map(fn (Collection $group, string $name) => [
                'name' => $name,
                'total' => round((float) $group->sum('amount'), 2),
            ])
            ->sortByDesc('total')
            ->values();
    }

    private function sumIncome(Collection $transactions): float
    {
        return (float) $transactions->where('type', TransactionType::Income)->sum('amount');
    }

    private function sumExpense(Collection $transactions): float
    {
        return (float) $transactions->where('type', TransactionType::Expense)->sum('amount');
    }

    /**
     * @return array<int, array{month: string, inflow: float, outflow: float, net: float}>
     */
    private function monthlyCashFlow(Collection $transactions): array
    {
        return $transactions
            ->groupBy(fn (Transaction $t) => $t->transaction_date->format('Y-m'))
            ->sortKeys()
            ->map(function (Collection $group, string $key) {
                $inflow = $this->sumIncome($group);
                $outflow = $this->sumExpense($group);

                return [
                    'month' => Carbon::createFromFormat('Y-m', $key)->format('M Y'),
                    'inflow' => round($inflow, 2),
                    'outflow' => round($outflow, 2),
                    'net' => round($inflow - $outflow, 2),
                ];
            })
            ->values()
            ->all();
    }
}
