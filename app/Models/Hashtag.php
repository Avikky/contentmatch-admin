<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hashtag extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'appserver';

    protected $fillable = [
        'name',
        'slug',
        'usage_count',
        'display_name',
        'description',
        'is_trending',
        'last_used_at',
    ];

    protected $casts = [
        'usage_count' => 'integer',
        'is_trending' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    // Relationships
    public function contents()
    {
        return $this->morphedByMany(Content::class, 'hashtaggable');
    }

    public function communities()
    {
        return $this->morphedByMany(Community::class, 'hashtaggable');
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'hashtaggable');
    }

    // Scopes
    public function scopeTrending($query)
    {
        return $query->where('is_trending', true);
    }

    public function scopePopular($query, int $minUsage = 10)
    {
        return $query->where('usage_count', '>=', $minUsage)
            ->orderByDesc('usage_count');
    }
}
