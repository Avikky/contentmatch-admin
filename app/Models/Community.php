<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Community extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'appserver';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'owner_id',
        'category_id',
        'purpose_id',
        'platform_id',
        'type',
        'banner_url',
        'status',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';

    const STATUS_SUSPENDED = 'suspended';

    const STATUS_ARCHIVED = 'archived';

    protected static function booted(): void
    {
        static::creating(function (Community $community) {
            if (empty($community->slug)) {
                $community->slug = Str::slug($community->name.'-'.Str::random(6));
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'community_members')
            ->withTimestamps()
            ->withPivot('role', 'status', 'notification_enabled', 'last_activity_at');
    }

    public function admins()
    {
        return $this->belongsToMany(User::class, 'community_members')
            ->wherePivot('role', 'admin')
            ->withTimestamps()
            ->withPivot('role', 'status', 'notification_enabled', 'last_activity_at');
    }

    public function moderators()
    {
        return $this->belongsToMany(User::class, 'community_members')
            ->wherePivot('role', 'moderator')
            ->withTimestamps()
            ->withPivot('role', 'status', 'notification_enabled', 'last_activity_at');
    }

    public function communityDiscord()
    {
        return $this->hasOne(CommunityDiscord::class);
    }

    public function hashtags()
    {
        return $this->morphToMany(Hashtag::class, 'hashtaggable');
    }

    public function engagementScores()
    {
        return $this->hasMany(CommunityEngagementScore::class);
    }

    public function rules()
    {
        return $this->hasMany(CommunityRule::class);
    }

    public function settings()
    {
        return $this->hasOne(CommunitySettings::class);
    }

    public function content()
    {
        return $this->hasMany(Content::class);
    }

    public function messages()
    {
        return $this->hasMany(CommunityMessage::class);
    }

    public function purposes()
    {
        return $this->belongsToMany(Purpose::class, 'community_purposes');
    }

    public function platforms()
    {
        return $this->belongsToMany(Platform::class, 'community_platforms');
    }
}
