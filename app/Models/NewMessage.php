<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewMessage extends Model
{
    protected $connection = 'appserver';

    protected $table = 'new_messages';

    protected $fillable = ['sender_id', 'receiver_id', 'message', 'type', 'is_request', 'read_at'];

    protected $casts = [
        'is_request' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
