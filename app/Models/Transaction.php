<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'type',
        'title',
        'amount',
        'description',
        'receipt_image_path',
        'transaction_date',
    ];

    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isIncome(): bool
    {
        return $this->type === TransactionType::Income;
    }

    public function isExpense(): bool
    {
        return $this->type === TransactionType::Expense;
    }

    public function amountPrefix(): string
    {
        return $this->type->amountPrefix();
    }

    public function amountColorClass(): string
    {
        return $this->type->amountColorClass();
    }

    public function typeBadgeClass(): string
    {
        return $this->type->badgeClass();
    }

    public function receiptImageUrl(): ?string
    {
        if (! filled($this->receipt_image_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->receipt_image_path);
    }
}
