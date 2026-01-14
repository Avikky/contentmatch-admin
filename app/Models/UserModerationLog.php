<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModerationLog extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $fillable = [
        'user_id',
        'admin_id',
        'action',
        'status_before',
        'status_after',
        'reason',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
