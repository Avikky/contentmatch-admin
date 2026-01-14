<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSocialOAuth extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $table = 'user_social_oauth';

    protected $fillable = [
        'user_id',
        'provider',
        'provider_user_id',
        'access_token',
    ];

    protected $hidden = [
        'access_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
