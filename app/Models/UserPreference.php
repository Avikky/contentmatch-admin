<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $fillable = [
        'user_id',
        'show_email',
        'show_stats',
        'allow_messages',
        'allow_mentions',
        'notification_preferences',
        'feed_preferences',
        'privacy_settings',
    ];

    protected $casts = [
        'show_email' => 'boolean',
        'show_stats' => 'boolean',
        'allow_messages' => 'boolean',
        'allow_mentions' => 'boolean',
        'notification_preferences' => 'json',
        'feed_preferences' => 'json',
        'privacy_settings' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
