<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageSettings extends Model
{
    protected $connection = 'appserver';

    protected $fillable = ['name', 'language_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
