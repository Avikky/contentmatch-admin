<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    const CONVERSATION_TYPE = 'direct';

    protected $connection = 'appserver';

    protected $fillable = [
        'type',
        'title',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    // Relationships
    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest(); // this is one Of Many
    }
}
