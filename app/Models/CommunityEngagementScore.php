<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityEngagementScore extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $fillable = [
        'community_id',
        'user_id',
        'engagement_score',
        'participation_rate',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'engagement_score' => 'integer',
        'participation_rate' => 'integer',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
