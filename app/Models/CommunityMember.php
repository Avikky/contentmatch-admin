<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityMember extends Model
{
    protected $connection = 'appserver';

    protected $table = 'community_members';

    protected $fillable = [
        'community_id',
        'user_id',
        'role',
        'status',
        'last_activity_at',
        'notification_enabled',
    ];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
