<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityDiscord extends Model
{
    protected $connection = 'appserver';

    protected $guarded = [];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }
}
