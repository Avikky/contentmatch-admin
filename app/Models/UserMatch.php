<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserMatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'appserver';

    protected $table = 'matches';

    protected $fillable = [
        'initiator_id',
        'receiver_id',
        'status',
        'matched_at',
    ];

    protected $casts = [
        'matched_at' => 'datetime',
        'status' => 'string',
    ];

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
