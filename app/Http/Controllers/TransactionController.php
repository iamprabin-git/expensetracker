<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Transaction::query()
            ->where('user_id', $request->user()->id)
            ->with('category')
            ->latest('transaction_date')
            ->latest('id');

        if ($type = $request->string('type')->toString()) {
            $query->where('type', $type);
        }

        if ($search = $request->string('search')->toString()) {
            $query->where('title', 'like', "%{$search}%");
        }

        $transactions = $query->paginate(12)->withQueryString();

        return view('transactions.index', compact('transactions'));
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

    protected function authorizeTransaction(Request $request, Transaction $transaction): void
    {
        abort_unless($transaction->user_id === $request->user()->id, 403);
    }
}
