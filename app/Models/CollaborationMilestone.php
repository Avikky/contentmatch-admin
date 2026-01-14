<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollaborationMilestone extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $fillable = [
        'collaboration_id',
        'title',
        'description',
        'is_completed',
        'due_date',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function collaboration()
    {
        return $this->belongsTo(Collaboration::class);
    }
}
