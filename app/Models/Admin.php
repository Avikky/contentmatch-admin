<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'role',
        'profile_photo',
        'is_active',
        'last_login_at',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the OTPs for the admin.
     */
    public function otps()
    {
        return $this->hasMany(AdminOtp::class);
    }

    /**
     * Get the admin who created this admin.
     */
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Get admins created by this admin.
     */
    public function createdAdmins()
    {
        return $this->hasMany(Admin::class, 'created_by');
    }

    /**
     * Check if admin is super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Check if admin is an admin.
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    /**
     * Check if admin is a moderator.
     */
    public function isModerator(): bool
    {
        return $this->role === 'moderator';
    }

    /**
     * Get profile photo URL or generate avatar.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo) {
            return asset('storage/'.$this->profile_photo);
        }

        // Generate avatar from initials
        $initials = $this->getInitials();

        return 'https://ui-avatars.com/api/?name='.urlencode($initials).'&color=fff&background=6366f1&size=200';
    }

    /**
     * Get initials from full name.
     */
    protected function getInitials(): string
    {
        $words = explode(' ', $this->full_name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1).substr($words[1], 0, 1));
        }

        return strtoupper(substr($this->full_name, 0, 2));
    }

    /**
     * Scope active admins.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Update last login timestamp.
     */
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }
}
