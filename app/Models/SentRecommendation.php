<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentRecommendation extends Model
{
    protected $fillable = ['user_id', 'item_id', 'type'];

    protected $connection = 'appserver';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
