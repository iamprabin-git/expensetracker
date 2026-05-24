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
     * @return Collection<int, Transaction>
     */
    public function periodTransactions(): Collection
    {
        return $this->queryThrough($this->fromDate, $this->toDate)->get();
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function cumulativeTransactions(): Collection
    {
        $end = $this->asOfDate()->toDateString();

        return $this->queryThrough(null, $end)->get();
    }

    /**
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
        $sections = [];

        $sectionDefs = [
            ['key' => 'income', 'title' => 'Income', 'type' => TransactionType::Income, 'side' => 'credit'],
            ['key' => 'expense', 'title' => 'Expenses', 'type' => TransactionType::Expense, 'side' => 'debit'],
            ['key' => 'asset', 'title' => 'Assets', 'type' => TransactionType::Asset, 'side' => 'debit'],
            ['key' => 'liability', 'title' => 'Liabilities', 'type' => TransactionType::Liability, 'side' => 'credit'],
        ];

        foreach ($sectionDefs as $def) {
            $sectionLines = $this->trialBalanceCategoryLines(
                $transactions,
                $def['type'],
                $def['side'],
            );
            $sections[] = [
                'key' => $def['key'],
                'title' => $def['title'],
                'lines' => $sectionLines,
                'subtotal_debit' => round(collect($sectionLines)->sum('debit'), 2),
                'subtotal_credit' => round(collect($sectionLines)->sum('credit'), 2),
            ];
            array_push($lines, ...$sectionLines);
        }

        $netCash = $this->netCashEffect($transactions);
        if (abs($netCash) >= 0.01) {
            $cashLine = [
                'account' => $netCash >= 0
                    ? 'Cash & cash equivalents (balancing)'
                    : 'Cash & cash equivalents (balancing)',
                'type' => 'cash',
                'debit' => $netCash >= 0 ? round($netCash, 2) : 0.0,
                'credit' => $netCash < 0 ? round(abs($netCash), 2) : 0.0,
            ];
            $lines[] = $cashLine;
            $sections[] = [
                'key' => 'cash',
                'title' => 'Balancing',
                'lines' => [$cashLine],
                'subtotal_debit' => $cashLine['debit'],
                'subtotal_credit' => $cashLine['credit'],
            ];
        }

        $totalDebit = round(collect($lines)->sum('debit'), 2);
        $totalCredit = round(collect($lines)->sum('credit'), 2);

        return [
            'sections' => $sections,
            'lines' => $lines,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'balanced' => abs($totalDebit - $totalCredit) < 0.01,
        ];
    }

    public function profitAndLoss(): array
    {
        $transactions = $this->periodTransactions();
        $revenue = $this->sumByCategory($transactions, TransactionType::Income);
        $expenses = $this->sumByCategory($transactions, TransactionType::Expense);

        $totalRevenue = $this->sumType($transactions, TransactionType::Income);
        $totalExpenses = $this->sumType($transactions, TransactionType::Expense);
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
        $beforePeriod = $this->transactionsBeforePeriod();
        $period = $this->periodTransactions();

        $assetRows = $this->sumByCategory($cumulative, TransactionType::Asset)
            ->map(fn (array $row) => ['name' => $row['name'], 'amount' => $row['total']])
            ->all();

        $cash = round($this->netCashEffect($cumulative), 2);
        $assetRows[] = ['name' => 'Cash and cash equivalents', 'amount' => $cash];

        $liabilityRows = $this->sumByCategory($cumulative, TransactionType::Liability)
            ->map(fn (array $row) => ['name' => $row['name'], 'amount' => $row['total']])
            ->all();

        $totalAssets = round(collect($assetRows)->sum('amount'), 2);
        $totalLiabilities = round(collect($liabilityRows)->sum('amount'), 2);

        $periodProfit = round(
            $this->sumType($period, TransactionType::Income) - $this->sumType($period, TransactionType::Expense),
            2,
        );

        $openingEquity = round($this->netEquity($beforePeriod), 2);
        $closingEquity = round($totalAssets - $totalLiabilities, 2);

        if ($this->fromDate) {
            $equityRows = [
                ['name' => 'Retained earnings (opening)', 'amount' => $openingEquity],
                [
                    'name' => $periodProfit >= 0 ? 'Profit for the period' : 'Loss for the period',
                    'amount' => $periodProfit,
                ],
            ];
        } else {
            $equityRows = [
                ['name' => 'Retained earnings (net worth)', 'amount' => $closingEquity],
            ];
            $periodProfit = $closingEquity;
            $openingEquity = 0.0;
        }

        $totalEquity = round(collect($equityRows)->sum('amount'), 2);

        return [
            'as_of' => $this->asOfDate()->format('M d, Y'),
            'assets' => $assetRows,
            'liabilities' => $liabilityRows,
            'equity' => $equityRows,
            'profit_loss' => [
                'period_label' => $this->periodLabel(),
                'total_revenue' => round($this->sumType($period, TransactionType::Income), 2),
                'total_expenses' => round($this->sumType($period, TransactionType::Expense), 2),
                'net_profit' => $periodProfit,
                'is_profit' => $periodProfit >= 0,
            ],
            'opening_equity' => $openingEquity,
            'period_profit' => $periodProfit,
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'total_liabilities_equity' => round($totalLiabilities + $totalEquity, 2),
            'balanced' => abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01,
        ];
    }

    public function cashFlow(): array
    {
        $period = $this->periodTransactions();
        $before = $this->transactionsBeforePeriod();

        $cashFromIncome = $this->sumType($period, TransactionType::Income);
        $cashPaidExpenses = $this->sumType($period, TransactionType::Expense);
        $assetOutflows = $this->sumType($period, TransactionType::Asset);
        $liabilityInflows = $this->sumType($period, TransactionType::Liability);

        $netOperating = $cashFromIncome - $cashPaidExpenses;
        $netInvesting = -$assetOutflows;
        $netFinancing = $liabilityInflows;
        $netChange = $netOperating + $netInvesting + $netFinancing;

        $openingCash = $this->netCashEffect($before);
        $closingCash = $openingCash + $netChange;

        $monthly = $this->monthlyCashFlow($period);

        return [
            'operating' => [
                ['label' => 'Cash received from income', 'amount' => round($cashFromIncome, 2)],
                ['label' => 'Cash paid for expenses', 'amount' => round(-$cashPaidExpenses, 2)],
                ['label' => 'Net cash from operating activities', 'amount' => round($netOperating, 2), 'bold' => true],
            ],
            'investing' => [
                ['label' => 'Asset purchases and investments', 'amount' => round(-$assetOutflows, 2)],
                ['label' => 'Net cash from investing activities', 'amount' => round($netInvesting, 2), 'bold' => true],
            ],
            'financing' => [
                ['label' => 'Borrowings and liability increases', 'amount' => round($liabilityInflows, 2)],
                ['label' => 'Net cash from financing activities', 'amount' => round($netFinancing, 2), 'bold' => true],
            ],
            'summary' => [
                'net_change' => round($netChange, 2),
                'opening_cash' => round($openingCash, 2),
                'closing_cash' => round($closingCash, 2),
            ],
            'monthly' => $monthly,
        ];
    }

    public function transactionStatement(): array
    {
        $transactions = $this->periodTransactions();
        $creditTypes = [TransactionType::Income, TransactionType::Asset];

        $credits = $transactions->filter(fn (Transaction $t) => in_array($t->type, $creditTypes, true))->sum('amount');
        $debits = $transactions->reject(fn (Transaction $t) => in_array($t->type, $creditTypes, true))->sum('amount');

        return [
            'transactions' => $transactions,
            'totals' => [
                'income' => (float) $this->sumType($transactions, TransactionType::Income),
                'expense' => (float) $this->sumType($transactions, TransactionType::Expense),
                'asset' => (float) $this->sumType($transactions, TransactionType::Asset),
                'liability' => (float) $this->sumType($transactions, TransactionType::Liability),
                'deposits' => round((float) $credits, 2),
                'withdrawals' => round((float) $debits, 2),
                'balance' => round($this->netCashEffect($transactions), 2),
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
     * @return array<int, array{account: string, type: string, debit: float, credit: float}>
     */
    private function trialBalanceCategoryLines(
        Collection $transactions,
        TransactionType $type,
        string $normalSide,
    ): array {
        $lines = [];

        foreach ($this->sumByCategory($transactions, $type) as $row) {
            $lines[] = [
                'account' => $row['name'].' ('.$type->label().')',
                'type' => $type->value,
                'debit' => $normalSide === 'debit' ? $row['total'] : 0.0,
                'credit' => $normalSide === 'credit' ? $row['total'] : 0.0,
            ];
        }

        return $lines;
    }

    /**
     * @return Collection<int, array{name: string, total: float}>
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

    private function sumType(Collection $transactions, TransactionType $type): float
    {
        return (float) $transactions->where('type', $type)->sum('amount');
    }

    /**
     * Net cash movement: income increases cash, expenses/assets decrease it, liabilities increase it.
     */
    private function netCashEffect(Collection $transactions): float
    {
        return $this->sumType($transactions, TransactionType::Income)
            - $this->sumType($transactions, TransactionType::Expense)
            - $this->sumType($transactions, TransactionType::Asset)
            + $this->sumType($transactions, TransactionType::Liability);
    }

    private function netEquity(Collection $transactions): float
    {
        return $this->netCashEffect($transactions)
            + $this->sumType($transactions, TransactionType::Asset)
            - $this->sumType($transactions, TransactionType::Liability);
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
                $inflow = $this->sumType($group, TransactionType::Income)
                    + $this->sumType($group, TransactionType::Liability);
                $outflow = $this->sumType($group, TransactionType::Expense)
                    + $this->sumType($group, TransactionType::Asset);

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
