<?php

namespace App\Enums;

enum TransactionType: string
{
    case Income = 'income';
    case Expense = 'expense';
    case Asset = 'asset';
    case Liability = 'liability';

    public function label(): string
    {
        return match ($this) {
            self::Income => 'Income',
            self::Expense => 'Expense',
            self::Asset => 'Asset',
            self::Liability => 'Liability',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Income => 'success',
            self::Expense => 'danger',
            self::Asset => 'primary',
            self::Liability => 'warning',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Income => 'text-bg-success',
            self::Expense => 'text-bg-danger',
            self::Asset => 'text-bg-primary',
            self::Liability => 'text-bg-warning',
        };
    }

    public function amountPrefix(): string
    {
        return match ($this) {
            self::Income, self::Asset => '+',
            self::Expense, self::Liability => '-',
        };
    }

    public function amountColorClass(): string
    {
        return match ($this) {
            self::Income => 'text-emerald-600 dark:text-emerald-400',
            self::Expense => 'text-rose-600 dark:text-rose-400',
            self::Asset => 'text-primary',
            self::Liability => 'text-warning',
        };
    }
}
