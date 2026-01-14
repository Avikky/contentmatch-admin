<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interest extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'appserver';

    protected $fillable = [
        'name',
        'slug',
        'icon_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_interests')
            ->withTimestamps();
    }

    public function content()
    {
        return $this->morphedByMany(Content::class, 'interestable');
    }

    public function communities()
    {
        return $this->morphedByMany(Community::class, 'interestable');
    }
}
