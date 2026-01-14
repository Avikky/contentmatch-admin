<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'appserver';

    protected $fillable = [
        'user_id',
        'type',
        'metadata',
        'is_pinned',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_pinned' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feedable()
    {
        return $this->morphTo();
    }
}
