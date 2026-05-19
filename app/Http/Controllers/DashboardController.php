<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $transactions = Transaction::query()
            ->where('user_id', $user->id)
            ->with('category')
            ->latest('transaction_date')
            ->latest('id')
            ->limit(8)
            ->get();

        $income = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', TransactionType::Income)
            ->sum('amount');

        $expense = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', TransactionType::Expense)
            ->sum('amount');

        $balance = $income - $expense;

        $monthlyIncome = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', TransactionType::Income)
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        $monthlyExpense = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', TransactionType::Expense)
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        return view('dashboard.index', compact(
            'transactions',
            'income',
            'expense',
            'balance',
            'monthlyIncome',
            'monthlyExpense',
        ));
    }
}
