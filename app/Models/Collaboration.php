<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collaboration extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'appserver';

    protected $fillable = [
        'initiator_id',
        'receiver_id',
        'title',
        'description',
        'platforms',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'platforms' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => 'string',
    ];

    // Relationships
    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function milestones()
    {
        return $this->hasMany(CollaborationMilestone::class);
    }
}
