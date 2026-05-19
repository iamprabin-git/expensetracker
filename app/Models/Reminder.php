<?php

namespace App\Models;

use App\Enums\ReminderFrequency;
use App\Enums\ReminderType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Reminder extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'payee_name',
        'amount',
        'notes',
        'frequency',
        'next_remind_at',
        'notify_email',
        'is_active',
        'last_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => ReminderType::class,
            'frequency' => ReminderFrequency::class,
            'amount' => 'decimal:2',
            'next_remind_at' => 'datetime',
            'notify_email' => 'boolean',
            'is_active' => 'boolean',
            'last_sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDueForEmail($query)
    {
        return $query
            ->where('is_active', true)
            ->where('notify_email', true)
            ->where('next_remind_at', '<=', now());
    }

    public function scopeDueNow($query)
    {
        return $query
            ->where('is_active', true)
            ->where('next_remind_at', '<=', now());
    }

    public function advanceAfterNotification(): void
    {
        if ($this->frequency === ReminderFrequency::Once) {
            $this->update([
                'is_active' => false,
                'last_sent_at' => now(),
            ]);

            return;
        }

        $next = match ($this->frequency) {
            ReminderFrequency::Daily => $this->next_remind_at->copy()->addDay(),
            ReminderFrequency::Weekly => $this->next_remind_at->copy()->addWeek(),
            ReminderFrequency::Monthly => $this->next_remind_at->copy()->addMonth(),
            ReminderFrequency::Yearly => $this->next_remind_at->copy()->addYear(),
            ReminderFrequency::Once => $this->next_remind_at,
        };

        while ($next->isPast()) {
            $next = match ($this->frequency) {
                ReminderFrequency::Daily => $next->copy()->addDay(),
                ReminderFrequency::Weekly => $next->copy()->addWeek(),
                ReminderFrequency::Monthly => $next->copy()->addMonth(),
                ReminderFrequency::Yearly => $next->copy()->addYear(),
                ReminderFrequency::Once => $next,
            };
        }

        $this->update([
            'next_remind_at' => $next,
            'last_sent_at' => now(),
        ]);
    }

    public static function buildNextRemindAt(
        string $date,
        string $time,
        ReminderFrequency $frequency,
        ?string $timezone = null,
    ): Carbon {
        $tz = $timezone ?: config('app.timezone');
        $datetime = Carbon::parse("{$date} {$time}", $tz);

        if ($datetime->isFuture() || $frequency === ReminderFrequency::Once) {
            return $datetime;
        }

        $next = $datetime->copy();

        while ($next->isPast()) {
            $next = match ($frequency) {
                ReminderFrequency::Daily => $next->copy()->addDay(),
                ReminderFrequency::Weekly => $next->copy()->addWeek(),
                ReminderFrequency::Monthly => $next->copy()->addMonth(),
                ReminderFrequency::Yearly => $next->copy()->addYear(),
                ReminderFrequency::Once => $next,
            };
        }

        return $next;
    }

    public function formattedAmount(?User $user = null): ?string
    {
        if ($this->amount === null) {
            return null;
        }

        $user ??= $this->user;

        return $user?->formatMoney((float) $this->amount) ?? number_format((float) $this->amount, 2);
    }
}
