<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $connection = 'appserver';

    protected $fillables = ['name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
