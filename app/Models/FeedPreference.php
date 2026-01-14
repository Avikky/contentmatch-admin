<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedPreference extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $fillable = [
        'user_id',
        'content_types',
        'platform_preferences',
        'category_preferences',
        'show_following_only',
    ];

    protected $casts = [
        'content_types' => 'json',
        'platform_preferences' => 'json',
        'category_preferences' => 'json',
        'show_following_only' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
