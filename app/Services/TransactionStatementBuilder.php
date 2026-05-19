<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TransactionStatementBuilder
{
    public function __construct(
        private readonly User $user,
        private readonly ?string $fromDate = null,
        private readonly ?string $toDate = null,
        private readonly ?string $type = null,
    ) {}

    public static function fromRequest(User $user, array $input): self
    {
        return new self(
            $user,
            $input['from_date'] ?? null,
            $input['to_date'] ?? null,
            $input['type'] ?? null,
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
            return 'Until '.Carbon::parse($this->toDate)->format('M d, Y');
        }

        return 'All transactions';
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function transactions(): Collection
    {
        $query = Transaction::query()
            ->where('user_id', $this->user->id)
            ->with('category')
            ->orderBy('transaction_date')
            ->orderBy('id');

        if ($this->fromDate) {
            $query->whereDate('transaction_date', '>=', $this->fromDate);
        }

        if ($this->toDate) {
            $query->whereDate('transaction_date', '<=', $this->toDate);
        }

        if ($this->type && in_array($this->type, ['income', 'expense'], true)) {
            $query->where('type', $this->type);
        }

        return $query->get();
    }

    public function totals(): array
    {
        $transactions = $this->transactions();

        $income = (float) $transactions
            ->where('type', TransactionType::Income)
            ->sum('amount');

        $expense = (float) $transactions
            ->where('type', TransactionType::Expense)
            ->sum('amount');

        return [
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
            'count' => $transactions->count(),
        ];
    }

    public function filters(): array
    {
        return [
            'from_date' => $this->fromDate,
            'to_date' => $this->toDate,
            'type' => $this->type,
        ];
    }
}
