<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class LoginTrail extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'login_trails';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'username',
        'action',
        'success',
        'ip_address',
        'user_agent',
        'additional_data',
        'session_id',
        'device_type',
        'browser',
        'platform',
        'reason',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'additional_data' => 'array',
        'success' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Action constants for consistency
     */
    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGOUT = 'logout';
    public const ACTION_FAILED_LOGIN = 'failed_login';
    public const ACTION_PASSWORD_CHANGE = 'password_change';
    public const ACTION_PASSWORD_RESET = 'password_reset';
    public const ACTION_PASSWORD_RESET_REQUEST = 'password_reset_request';
    public const ACTION_ACCOUNT_LOCKED = 'account_locked';
    public const ACTION_ACCOUNT_UNLOCKED = 'account_unlocked';
    public const ACTION_SESSION_EXPIRED = 'session_expired';
    public const ACTION_FORCED_LOGOUT = 'forced_logout';

    /**
     * Get all available actions
     */
    public static function getActions(): array
    {
        return [
            self::ACTION_LOGIN,
            self::ACTION_LOGOUT,
            self::ACTION_FAILED_LOGIN,
            self::ACTION_PASSWORD_CHANGE,
            self::ACTION_PASSWORD_RESET,
            self::ACTION_PASSWORD_RESET_REQUEST,
            self::ACTION_ACCOUNT_LOCKED,
            self::ACTION_ACCOUNT_UNLOCKED,
            self::ACTION_SESSION_EXPIRED,
            self::ACTION_FORCED_LOGOUT,
        ];
    }

    /**
     * Get the user that owns the login trail.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by action
     */
    public function scopeByAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by success status
     */
    public function scopeBySuccess(Builder $query, bool $success = true): Builder
    {
        return $query->where('success', $success);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by IP address
     */
    public function scopeByIpAddress(Builder $query, string $ipAddress): Builder
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope to filter recent records
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for search functionality
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('username', 'like', "%{$search}%")
              ->orWhere('ip_address', 'like', "%{$search}%")
              ->orWhere('action', 'like', "%{$search}%")
              ->orWhere('reason', 'like', "%{$search}%")
              ->orWhereHas('user', function ($userQuery) use ($search) {
                  $userQuery->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
              });
        });
    }
}