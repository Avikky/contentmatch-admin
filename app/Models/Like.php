<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $connection = 'appserver';

    protected $fillable = ['user_id', 'likeable_id', 'likeable_type'];

    public function likeable()
    {
        return $this->morphTo();
    }

    // Define the relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
