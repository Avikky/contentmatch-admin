<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunitySettings extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $fillable = [
        'community_id',
        'daily_post_limit',
        'weekly_engagement_requirement',
        'min_engagement_to_post',
    ];

    protected $casts = [
        'daily_post_limit' => 'integer',
        'weekly_engagement_requirement' => 'integer',
        'min_engagement_to_post' => 'integer',
    ];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }
}
