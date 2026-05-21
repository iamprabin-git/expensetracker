<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;

class TransactionListExportService
{
    /**
     * @return Collection<int, Transaction>
     */
    public function transactions(User $user, array $filters): Collection
    {
        $search = $filters['search'] ?? '';
        $type = $filters['type'] ?? '';

        return Transaction::query()
            ->where('user_id', $user->id)
            ->when($type !== '', fn ($q) => $q->where('type', $type))
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->with('category')
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return array{headers: list<string>, rows: list<list<string>>}
     */
    public function dataset(User $user, Collection $transactions): array
    {
        $rows = [];

        foreach ($transactions as $transaction) {
            $rows[] = [
                $transaction->transaction_date->format('Y-m-d'),
                $transaction->title,
                $transaction->description ?? '',
                $transaction->type->label(),
                $transaction->category?->name ?? '',
                $transaction->amountPrefix().$user->formatMoney((float) $transaction->amount),
                $transaction->receipt_image_path ? 'Yes' : 'No',
            ];
        }

        return [
            'headers' => ['Date', 'Title', 'Description', 'Type', 'Category', 'Amount', 'Receipt'],
            'rows' => $rows,
        ];
    }

    public function filename(string $extension): string
    {
        return 'transactions-'.now()->format('Y-m-d').'.'.$extension;
    }
}
