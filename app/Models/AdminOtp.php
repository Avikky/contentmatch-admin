<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class AdminOtp extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     protected $connection = 'mysql';
    protected $fillable = [
        'admin_id',
        'otp_hash',
        'expires_at',
        'used_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    /**
     * Get the admin that owns the OTP.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Generate a 6-digit OTP.
     */
    public static function generateOtp(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create OTP for admin.
     */
    public static function createForAdmin(int $adminId, string $otp): self
    {
        // Invalidate all previous OTPs for this admin
        static::where('admin_id', $adminId)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->delete();

        // Create new OTP
        return static::create([
            'admin_id' => $adminId,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
        ]);
    }

    /**
     * Verify OTP.
     */
    public function verify(string $otp): bool
    {
        // Check if already used
        if ($this->used_at !== null) {
            return false;
        }

        // Check if expired
        if ($this->expires_at < now()) {
            return false;
        }

        // Verify OTP
        if (! Hash::check($otp, $this->otp_hash)) {
            return false;
        }

        // Mark as used
        $this->update(['used_at' => now()]);

        return true;
    }

    /**
     * Check if OTP is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at < now();
    }

    /**
     * Check if OTP is used.
     */
    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    /**
     * Scope to find valid OTP for admin.
     */
    public function scopeValidForAdmin($query, int $adminId)
    {
        return $query->where('admin_id', $adminId)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest();
    }
}
