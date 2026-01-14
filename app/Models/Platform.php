<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Platform extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'appserver';

    protected $fillable = [
        'name',
        'display_name',
        'icon_path',
        'is_active',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_platform_links')
            ->withPivot(['platform_username', 'platform_user_id', 'platform_url', 'verified_at'])
            ->withTimestamps();
    }

    public function communities()
    {
        return $this->belongsToMany(Community::class, 'community_platforms');
    }
}
