<?php

namespace App\Enums;

enum CategoryType: string
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

    public function badgeClass(): string
    {
        return match ($this) {
            self::Income => 'text-bg-success',
            self::Expense => 'text-bg-danger',
            self::Asset => 'text-bg-primary',
            self::Liability => 'text-bg-warning',
        };
    }

    public function filamentColor(): string
    {
        return match ($this) {
            self::Income => 'success',
            self::Expense => 'danger',
            self::Asset => 'info',
            self::Liability => 'warning',
        };
    }
}
