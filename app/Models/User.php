<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'email',
    'google_id',
    'google_token',
    'password',
    'role',
    'avatar_path',
    'phone',
    'currency',
    'timezone',
    'locale',
    'is_approved',
    'approved_at',
    'membership_fee',
    'membership_expires_at',
])]
#[Hidden(['password', 'remember_token', 'google_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_approved' => 'boolean',
            'approved_at' => 'datetime',
            'membership_fee' => 'decimal:2',
            'membership_expires_at' => 'datetime',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === UserRole::Admin && $panel->getId() === 'admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isRegularUser(): bool
    {
        return $this->role === UserRole::User;
    }

    public function isApproved(): bool
    {
        return $this->isAdmin() || $this->is_approved;
    }

    public function hasActiveMembership(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (! $this->isApproved()) {
            return false;
        }

        if ($this->membership_expires_at === null) {
            return true;
        }

        return $this->membership_expires_at->isFuture();
    }

    public function approve(?float $membershipFee = null, ?\DateTimeInterface $expiresAt = null): void
    {
        $wasApproved = $this->is_approved;

        $this->update([
            'is_approved' => true,
            'approved_at' => now(),
            'membership_fee' => $membershipFee ?? $this->membership_fee,
            'membership_expires_at' => $expiresAt ?? $this->membership_expires_at ?? now()->addYear(),
        ]);

        if (! $wasApproved && $this->isRegularUser()) {
            $this->notify(new \App\Notifications\AccountApprovedNotification);
        }
    }

    public function reject(): void
    {
        $this->update([
            'is_approved' => false,
            'approved_at' => null,
        ]);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function avatarUrl(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        return Storage::disk('public')->url($this->avatar_path);
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];

        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1).substr(end($parts), 0, 1));
        }

        return strtoupper(substr($this->name, 0, 2));
    }

    public function formatMoney(float $amount): string
    {
        $code = $this->currency ?? 'USD';
        $config = config("currencies.{$code}", config('currencies.USD'));
        $decimals = $config['decimals'] ?? 2;
        $symbol = $config['symbol'] ?? '$';

        return $symbol.number_format($amount, $decimals);
    }
}
