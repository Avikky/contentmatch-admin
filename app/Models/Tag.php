<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'appserver';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_trending',
        'usage_count',
        'is_active',
    ];

    protected $casts = [
        'is_trending' => 'boolean',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
    ];

    // Relationships
    public function contents()
    {
        return $this->morphedByMany(Content::class, 'taggable');
    }

    public function communities()
    {
        return $this->morphedByMany(Community::class, 'taggable');
    }

    // Scopes
    public function scopeTrending($query)
    {
        return $query->where('is_trending', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Relationship with communities
}
