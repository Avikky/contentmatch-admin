<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngagementMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_id',
        'recorded_for',
        'posts',
        'comments',
        'likes',
        'shares',
        'impressions',
    ];

    protected $casts = [
        'recorded_for' => 'date',
    ];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }
}
