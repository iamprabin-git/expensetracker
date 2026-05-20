<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetPlan extends Model
{
    protected $fillable = [
        'user_id',
        'period_month',
        'name',
        'income_goal',
        'expense_limit',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'period_month' => 'date',
            'income_goal' => 'decimal:2',
            'expense_limit' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }

    public function periodLabel(): string
    {
        return $this->period_month->format('F Y');
    }
}
