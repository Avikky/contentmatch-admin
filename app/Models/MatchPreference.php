<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchPreference extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $fillable = [
        'user_id',
        'preferred_categories',
        'preferred_platforms',
    ];

    protected $casts = [
        'preferred_categories' => 'json',
        'preferred_platforms' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
