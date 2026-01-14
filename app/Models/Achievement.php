<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $fillable = [
        'name',
        'description',
        'icon_url',
        'points',
        'requirements',
        'is_active',
    ];

    protected $casts = [
        'requirements' => 'array',
        'is_active' => 'boolean',
        'points' => 'integer',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot(['achieved_at', 'metadata'])
            ->withTimestamps();
    }
}
