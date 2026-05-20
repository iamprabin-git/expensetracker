<?php

namespace App\Http\Controllers;

use App\Services\TransactionStatementBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TransactionStatementController extends Controller
{
    public function show(Request $request): View
    {
        $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'type' => ['nullable', 'in:income,expense,asset,liability'],
        ]);

        $statement = TransactionStatementBuilder::fromRequest($request->user(), $request->all());

        return view('transactions.statement', [
            'user' => $request->user(),
            'statement' => $statement,
            'transactions' => $statement->transactions(),
            'totals' => $statement->totals(),
            'periodLabel' => $statement->periodLabel(),
            'filters' => $statement->filters(),
        ]);
    }

    public function pdf(Request $request): Response
    {
        $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'type' => ['nullable', 'in:income,expense,asset,liability'],
        ]);

        $statement = TransactionStatementBuilder::fromRequest($request->user(), $request->all());

        $filename = 'transaction-statement-'.now()->format('Y-m-d').'.pdf';

        $pdf = Pdf::loadView('transactions.statement-pdf', [
            'user' => $request->user(),
            'transactions' => $statement->transactions(),
            'totals' => $statement->totals(),
            'periodLabel' => $statement->periodLabel(),
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }
}
