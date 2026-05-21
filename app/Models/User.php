<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Notifications\AccountApprovedNotification;
use App\Notifications\UserPasswordResetNotification;
use App\Services\ReceiptScanService;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'email',
    'google_id',
    'google_token',
    'google_avatar',
    'password',
    'role',
    'avatar_path',
    'phone',
    'currency',
    'timezone',
    'locale',
    'notification_sound_enabled',
    'ai_scan_enabled',
    'is_approved',
    'approved_at',
    'membership_fee',
    'membership_expires_at',
])]
#[Hidden(['password', 'remember_token', 'google_token'])]
class User extends Authenticatable implements FilamentUser, HasAvatar
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
            'notification_sound_enabled' => 'boolean',
            'ai_scan_enabled' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === UserRole::Admin && $panel->getId() === 'admin';
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->profilePhotoUrl();
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

    public function hasAiScanAccess(): bool
    {
        if ($this->isAdmin()) {
            return false;
        }

        return (bool) $this->ai_scan_enabled;
    }

    public function canUseAiScan(): bool
    {
        if (! $this->hasAiScanAccess()) {
            return false;
        }

        return app(ReceiptScanService::class)->isConfigured();
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

    public function approve(?float $membershipFee = null, \DateTimeInterface|string|null $expiresAt = null): void
    {
        $wasApproved = $this->is_approved;

        if (is_string($expiresAt)) {
            $expiresAt = Carbon::parse($expiresAt);
        }

        $this->update([
            'is_approved' => true,
            'approved_at' => now(),
            'membership_fee' => $membershipFee ?? $this->membership_fee,
            'membership_expires_at' => $expiresAt ?? $this->membership_expires_at ?? now()->addYear(),
            'email_verified_at' => $this->email_verified_at ?? now(),
        ]);

        if (! $wasApproved && $this->isRegularUser()) {
            $this->notify(new AccountApprovedNotification);
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

    public function budgetPlans(): HasMany
    {
        return $this->hasMany(BudgetPlan::class);
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

    /** Uploaded avatar, else Google profile photo from sign-in. */
    public function profilePhotoUrl(): ?string
    {
        if ($this->avatar_path) {
            return $this->avatarUrl();
        }

        if (filled($this->google_avatar)) {
            return $this->google_avatar;
        }

        return null;
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];

        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1).substr(end($parts), 0, 1));
        }

        return strtoupper(substr($this->name, 0, 2));
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new UserPasswordResetNotification($token));
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
