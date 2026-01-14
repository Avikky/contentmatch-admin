<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityPurposeTemplate extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function communities()
    {
        return $this->hasMany(Community::class, 'purpose_template_id');
    }
}
