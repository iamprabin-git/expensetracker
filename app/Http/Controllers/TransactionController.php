<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\TransactionListExportService;
use App\Support\TabularExporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $search = $request->string('search')->toString();
        $type = $request->string('type')->toString();

        $filtered = Transaction::query()
            ->where('user_id', $userId)
            ->when($type !== '', fn ($q) => $q->where('type', $type))
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%"));

        $summary = [
            'count' => (clone $filtered)->count(),
            'income' => (float) (clone $filtered)->where('type', TransactionType::Income)->sum('amount'),
            'expense' => (float) (clone $filtered)->where('type', TransactionType::Expense)->sum('amount'),
        ];
        $summary['balance'] = $summary['income'] - $summary['expense'];

        $transactions = (clone $filtered)
            ->with('category')
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('transactions.index', [
            'transactions' => $transactions,
            'summary' => $summary,
            'hasFilters' => $search !== '' || $type !== '',
        ]);
    }

    public function create(Request $request): View
    {
        $categories = Category::forUser($request->user())->orderBy('name')->get();

        return view('transactions.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::enum(TransactionType::class)],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(function ($query) use ($request) {
                    $query->whereNull('user_id')->orWhere('user_id', $request->user()->id);
                }),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'transaction_date' => ['required', 'date'],
        ]);

        $request->user()->transactions()->create($validated);

        return redirect()->route('transactions.index')->with('success', 'Transaction recorded successfully.');
    }

    public function edit(Request $request, Transaction $transaction): View
    {
        $this->authorizeTransaction($request, $transaction);

        $categories = Category::forUser($request->user())->orderBy('name')->get();

        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->authorizeTransaction($request, $transaction);

        $validated = $request->validate([
            'type' => ['required', Rule::enum(TransactionType::class)],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(function ($query) use ($request) {
                    $query->whereNull('user_id')->orWhere('user_id', $request->user()->id);
                }),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'transaction_date' => ['required', 'date'],
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->authorizeTransaction($request, $transaction);

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaction deleted.');
    }

    public function export(Request $request, string $format): StreamedResponse
    {
        abort_unless(in_array($format, ['csv', 'xlsx'], true), 404);

        $service = new TransactionListExportService;
        $filters = $request->only(['search', 'type']);
        $transactions = $service->transactions($request->user(), $filters);
        $dataset = $service->dataset($request->user(), $transactions);
        $extension = $format === 'xlsx' ? 'xlsx' : 'csv';
        $filename = $service->filename($extension);

        return match ($format) {
            'csv' => TabularExporter::downloadCsv($filename, $dataset['headers'], $dataset['rows']),
            'xlsx' => TabularExporter::downloadXlsx($filename, $dataset['headers'], $dataset['rows']),
        };
    }

    protected function authorizeTransaction(Request $request, Transaction $transaction): void
    {
        abort_unless($transaction->user_id === $request->user()->id, 403);
    }
}
