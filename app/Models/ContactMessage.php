<?php

namespace App\Models;

use App\Mail\ContactMessageReplyMail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'admin_reply',
        'is_read',
        'read_at',
        'replied_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'replied_at' => 'datetime',
        ];
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function hasReply(): bool
    {
        return filled($this->admin_reply);
    }

    public function sendReply(string $body): void
    {
        $this->update([
            'admin_reply' => $body,
            'replied_at' => now(),
            'is_read' => true,
            'read_at' => $this->read_at ?? now(),
        ]);

        Mail::to($this->email)->send(new ContactMessageReplyMail($this->fresh()));
    }
}
