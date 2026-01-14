<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Purpose extends Model
{
    protected $connection = 'appserver';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_purposes')
            ->withTimestamps();
    }

    // Relationship with communities
    public function communities()
    {
        return $this->belongsToMany(Community::class, 'community_purposes');
    }
}
