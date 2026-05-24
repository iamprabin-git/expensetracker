<?php

namespace App\Models;

use App\Enums\CategoryType;
use App\Support\CategoryIcons;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'icon',
    ];

    public function resolvedIcon(): string
    {
        return CategoryIcons::resolve($this);
    }

    protected function casts(): array
    {
        return [
            'type' => CategoryType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->whereNull('user_id')->orWhere('user_id', $user->id);
        });
    }

    public function scopeSystem($query)
    {
        return $query->whereNull('user_id');
    }

    public function isUserOwned(): bool
    {
        return $this->user_id !== null;
    }
}
