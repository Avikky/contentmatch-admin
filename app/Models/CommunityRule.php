<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityRule extends Model
{
    protected $connection = 'appserver';

    protected $fillable = [
        'community_id',
        'template_id',
        'title',
        'description',
        'order',
        'is_custom',
    ];

    protected $casts = [
        'is_custom' => 'boolean',
        'order' => 'integer',
    ];

    public function template()
    {
        return $this->belongsTo(CommunityRuleTemplate::class, 'template_id');
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }
}
