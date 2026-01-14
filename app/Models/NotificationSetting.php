<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $table = 'notification_settings';

    protected $fillable = [
        'user_id',
        'email_notifications',
        'push_notifications',
        'engagement_notifications',
        'match_notifications',
        'message_notifications',
        'preferences',
    ];

    protected $casts = [
        'email_notifications' => 'integer',
        'push_notifications' => 'integer',
        'message_notifications' => 'integer',
        'match_notifications' => 'integer',
        'engagement_notifications' => 'integer',
        'preferences' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
