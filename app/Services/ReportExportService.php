<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;

class ReportExportService
{
    public function __construct(
        private readonly User $user,
        private readonly FinancialReportBuilder $builder,
        private readonly string $reportKey,
        private readonly string $reportTitle,
    ) {}

    /**
     * @return array{headers: list<string>, rows: list<list<string|float|int|null>>}
     */
    public function dataset(): array
    {
        return match ($this->reportKey) {
            'trial-balance' => $this->trialBalanceDataset(),
            'profit-loss' => $this->profitLossDataset(),
            'balance-sheet' => $this->balanceSheetDataset(),
            'cash-flow' => $this->cashFlowDataset(),
            'transaction-statement' => $this->transactionStatementDataset(),
            default => ['headers' => [], 'rows' => []],
        };
    }

    public function filename(string $extension): string
    {
        $slug = str_replace(' ', '-', strtolower($this->reportTitle));

        return $slug.'-'.now()->format('Y-m-d').'.'.$extension;
    }

    /**
     * @return array{headers: list<string>, rows: list<list<string>>}
     */
    private function trialBalanceDataset(): array
    {
        $report = $this->builder->trialBalance();
        $rows = [];

        foreach ($report['sections'] as $section) {
            $rows[] = [$section['title'], '', ''];

            foreach ($section['lines'] as $line) {
                $rows[] = [
                    $line['account'],
                    $line['debit'] > 0 ? $this->user->formatMoney($line['debit']) : '',
                    $line['credit'] > 0 ? $this->user->formatMoney($line['credit']) : '',
                ];
            }

            if ($section['lines'] !== []) {
                $rows[] = [
                    $section['title'].' subtotal',
                    $this->user->formatMoney($section['subtotal_debit']),
                    $this->user->formatMoney($section['subtotal_credit']),
                ];
            }
        }

        if ($rows !== []) {
            $rows[] = [
                'Grand total',
                $this->user->formatMoney($report['total_debit']),
                $this->user->formatMoney($report['total_credit']),
            ];
        }

        return [
            'headers' => ['Account', 'Debit', 'Credit'],
            'rows' => $rows,
        ];
    }

    /**
     * @return array{headers: list<string>, rows: list<list<string>>}
     */
    private function profitLossDataset(): array
    {
        $report = $this->builder->profitAndLoss();
        $rows = [['Revenue', '', '']];

        foreach ($report['revenue'] as $row) {
            $rows[] = [$row['name'], $this->user->formatMoney($row['total']), ''];
        }

        $rows[] = ['Total revenue', $this->user->formatMoney($report['total_revenue']), ''];
        $rows[] = ['', '', ''];
        $rows[] = ['Expenses', '', ''];

        foreach ($report['expenses'] as $row) {
            $rows[] = [$row['name'], $this->user->formatMoney($row['total']), ''];
        }

        $rows[] = ['Total expenses', $this->user->formatMoney($report['total_expenses']), ''];
        $rows[] = ['Net '.($report['is_profit'] ? 'profit' : 'loss'), $this->user->formatMoney($report['net_profit']), ''];

        return [
            'headers' => ['Category', 'Amount', ''],
            'rows' => $rows,
        ];
    }

    /**
     * @return array{headers: list<string>, rows: list<list<string>>}
     */
    private function balanceSheetDataset(): array
    {
        $report = $this->builder->balanceSheet();
        $pl = $report['profit_loss'];
        $rows = [
            ['As of', $report['as_of'], ''],
            ['', '', ''],
            ['Profit & loss ('.$pl['period_label'].')', '', ''],
            ['Revenue', $this->user->formatMoney($pl['total_revenue']), ''],
            ['Expenses', $this->user->formatMoney($pl['total_expenses']), ''],
            ['Net '.($pl['is_profit'] ? 'profit' : 'loss'), $this->user->formatMoney($pl['net_profit']), ''],
            ['', '', ''],
            ['Assets', '', ''],
        ];

        foreach ($report['assets'] as $row) {
            $rows[] = [$row['name'], $this->user->formatMoney($row['amount']), ''];
        }

        $rows[] = ['Total assets', $this->user->formatMoney($report['total_assets']), ''];
        $rows[] = ['', '', ''];
        $rows[] = ['Liabilities', '', ''];

        foreach ($report['liabilities'] as $row) {
            $rows[] = [$row['name'], $this->user->formatMoney($row['amount']), ''];
        }

        $rows[] = ['Total liabilities', $this->user->formatMoney($report['total_liabilities']), ''];
        $rows[] = ['', '', ''];
        $rows[] = ['Equity', '', ''];

        foreach ($report['equity'] as $row) {
            $rows[] = [$row['name'], $this->user->formatMoney($row['amount']), ''];
        }

        $rows[] = ['Total equity', $this->user->formatMoney($report['total_equity']), ''];
        $rows[] = ['Liabilities + equity', $this->user->formatMoney($report['total_liabilities_equity']), ''];

        return [
            'headers' => ['Line item', 'Amount', ''],
            'rows' => $rows,
        ];
    }

    /**
     * @return array{headers: list<string>, rows: list<list<string>>}
     */
    private function cashFlowDataset(): array
    {
        $report = $this->builder->cashFlow();
        $rows = [['Operating activities', '', '']];

        foreach ($report['operating'] as $row) {
            $rows[] = [$row['label'], $this->user->formatMoney($row['amount']), ''];
        }

        $rows[] = ['', '', ''];
        $rows[] = ['Investing activities', '', ''];

        foreach ($report['investing'] as $row) {
            $rows[] = [$row['label'], $this->user->formatMoney($row['amount']), ''];
        }

        $rows[] = ['', '', ''];
        $rows[] = ['Financing activities', '', ''];

        foreach ($report['financing'] as $row) {
            $rows[] = [$row['label'], $this->user->formatMoney($row['amount']), ''];
        }

        $rows[] = ['', '', ''];
        $rows[] = ['Net change in cash', $this->user->formatMoney($report['summary']['net_change']), ''];
        $rows[] = ['Opening cash', $this->user->formatMoney($report['summary']['opening_cash']), ''];
        $rows[] = ['Closing cash', $this->user->formatMoney($report['summary']['closing_cash']), ''];

        if ($report['monthly'] !== []) {
            $rows[] = ['', '', ''];
            $rows[] = ['Monthly breakdown', '', ''];

            foreach ($report['monthly'] as $month) {
                $rows[] = [
                    $month['month'],
                    'In: '.$this->user->formatMoney($month['inflow']).' / Out: '.$this->user->formatMoney($month['outflow']),
                    'Net: '.$this->user->formatMoney($month['net']),
                ];
            }
        }

        return [
            'headers' => ['Item', 'Amount', 'Notes'],
            'rows' => $rows,
        ];
    }

    /**
     * @return array{headers: list<string>, rows: list<list<string>>}
     */
    private function transactionStatementDataset(): array
    {
        $statement = $this->builder->transactionStatement();
        /** @var Collection<int, Transaction> $transactions */
        $transactions = $statement['transactions'];
        $creditTypes = [TransactionType::Income, TransactionType::Asset];
        $rows = [];
        $running = 0.0;

        foreach ($transactions as $transaction) {
            $amount = (float) $transaction->amount;
            $isCredit = in_array($transaction->type, $creditTypes, true);
            $running += $isCredit ? $amount : -$amount;

            $rows[] = [
                $transaction->transaction_date->format('Y-m-d'),
                $transaction->title,
                $transaction->category?->name ?? '',
                $transaction->type->label(),
                $isCredit ? '' : $this->user->formatMoney($amount),
                $isCredit ? $this->user->formatMoney($amount) : '',
                $this->user->formatMoney($running),
            ];
        }

        if ($rows !== []) {
            $totals = $statement['totals'];
            $rows[] = [
                '',
                'Period totals',
                '',
                '',
                $this->user->formatMoney($totals['withdrawals'] ?? $totals['expense']),
                $this->user->formatMoney($totals['deposits'] ?? $totals['income']),
                $this->user->formatMoney($totals['balance']),
            ];
        }

        return [
            'headers' => ['Date', 'Title', 'Category', 'Type', 'Withdrawals', 'Deposits', 'Balance'],
            'rows' => $rows,
        ];
    }
}
