<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityMessage extends Model
{
    protected $connection = 'appserver';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }
}
