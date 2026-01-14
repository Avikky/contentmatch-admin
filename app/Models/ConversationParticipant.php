<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationParticipant extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'last_read_at',
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
    ];

    // Relationships
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update(['last_read_at' => now()]);
    }

    public function hasUnreadMessages()
    {
        if (! $this->last_read_at) {
            return $this->conversation->messages()->exists();
        }

        return $this->conversation->messages()
            ->where('created_at', '>', $this->last_read_at)
            ->where('user_id', '!=', $this->user_id)
            ->exists();
    }
}
