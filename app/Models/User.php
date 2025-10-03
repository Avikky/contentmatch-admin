<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'username',
    //     'password',
    // ];
    protected $guarded = [];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'unencrypted_password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    protected $appends = [
        'display_name',
        'initials',
    ];

    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopeNonAdmins($query)
    {
        return $query->where('is_admin', false);
    }

    public function communities()
    {
        return $this->belongsToMany(Community::class)
            ->withTimestamps()
            ->withPivot('role');
    }

    public function managedCommunities()
    {
        return $this->hasMany(Community::class, 'owner_id');
    }

    public function displayName(): Attribute
    {
        return Attribute::get(function ($value, $attributes) {
            return $value ?: ($attributes['full_name'] ?? $attributes['name']);
        });
    }

    public function initials(): Attribute
    {
        return Attribute::get(function ($value, $attributes) {
            $displayName = $this->display_name ?? ($attributes['full_name'] ?? $attributes['name'] ?? 'CM');

            return collect(explode(' ', $displayName))
                ->filter()
                ->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))
                ->take(2)
                ->implode('') ?: 'CM';
        });
    }

    public static function generateIdentifier(string $prefix = 'USR'): string
    {
        do {
            $candidate = sprintf('%s-%s', strtoupper($prefix), Str::upper(Str::random(6)));
        } while (static::where('user_id', $candidate)->exists());

        return $candidate;
    }
}
