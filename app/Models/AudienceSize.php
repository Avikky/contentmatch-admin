<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudienceSize extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $fillable = [
        'name',
        'slug',
        'min_followers',
        'max_followers',
        'label',
        'is_active',
    ];

    protected $casts = [
        'min_followers' => 'integer',
        'max_followers' => 'integer',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_audience_sizes')
            ->withTimestamps();
    }
}
