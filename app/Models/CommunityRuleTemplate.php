<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityRuleTemplate extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $fillable = [
        'title',
        'description',
        'category',
        'order',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function rules()
    {
        return $this->hasMany(CommunityRule::class, 'template_id');
    }
}
