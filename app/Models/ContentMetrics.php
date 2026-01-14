<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentMetrics extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'appserver';

    protected $fillable = [
        'content_id',
        'views_count',
        'likes_count',
        'comments_count',
        'shares_count',
        'engagement_rate',
    ];

    protected $casts = [
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'shares_count' => 'integer',
        'engagement_rate' => 'integer',
    ];

    // Relationships
    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}
