<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $income = Transaction::query()->where('type', TransactionType::Income)->sum('amount');
        $expense = Transaction::query()->where('type', TransactionType::Expense)->sum('amount');

        return [
            Stat::make('Total Users', User::query()->where('role', \App\Enums\UserRole::User)->count())
                ->description('Registered app users')
                ->color('primary'),
            Stat::make('Total Income', '$'.number_format($income, 2))
                ->description('All recorded income')
                ->color('success'),
            Stat::make('Total Expenses', '$'.number_format($expense, 2))
                ->description('All recorded expenses')
                ->color('danger'),
            Stat::make('Net Balance', '$'.number_format($income - $expense, 2))
                ->description('Income minus expenses')
                ->color('warning'),
        ];
    }
}
