<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnalytics extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $fillable = [
        'user_id',
        'total_engagements',
        'total_feedback_received',
        'total_feedback_given',
        'total_matches',
        'average_rating',
        'platform_metrics',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'total_engagements' => 'integer',
        'total_feedback_received' => 'integer',
        'total_feedback_given' => 'integer',
        'total_matches' => 'integer',
        'average_rating' => 'float',
        'platform_metrics' => 'json',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
