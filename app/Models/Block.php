<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $fillable = [
        'blocker_id',
        'blocked_id',
        'reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function blocker()
    {
        return $this->belongsToMany(User::class, 'blocker_id');
    }

    public function blocked()
    {
        return $this->belongsToMany(User::class, 'blocked_id');
    }

    // Helper Methods
    public static function isBlocked($blockerId, $blockedId): bool
    {
        return static::where('blocker_id', $blockerId)
            ->where('blocked_id', $blockedId)
            ->exists();
    }

    public static function areBlocked($userId1, $userId2): bool
    {
        return static::where(function ($query) use ($userId1, $userId2) {
            $query->where('blocker_id', $userId1)
                ->where('blocked_id', $userId2);
        })->orWhere(function ($query) use ($userId1, $userId2) {
            $query->where('blocker_id', $userId2)
                ->where('blocked_id', $userId1);
        })->exists();
    }

    public static function getBlockedUsers($userId)
    {
        return static::where('blocker_id', $userId)
            ->with('blocked')
            ->get()
            ->pluck('blocked');
    }

    public static function getBlockedBy($userId)
    {
        return static::where('blocked_id', $userId)
            ->with('blocker')
            ->get()
            ->pluck('blocker');
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('blocker_id', $userId)
            ->orWhere('blocked_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    // Events
    protected static function booted()
    {
        static::created(function ($block) {
            // Could trigger notifications or other events
            // Could update user relationships/status
        });

        static::deleted(function ($block) {
            // Could trigger notifications or other events
            // Could restore user relationships/status
        });
    }
}
