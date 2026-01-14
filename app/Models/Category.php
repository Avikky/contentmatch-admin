<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $fillable = [
        'name',
        'slug',
        'icon_url',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_categories')
            ->withTimestamps();
    }

    public function content()
    {
        return $this->morphedByMany(Content::class, 'categorizable');
    }

    public function communities()
    {
        return $this->morphedByMany(Community::class, 'categorizable');
    }
}
