<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingStep extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'order',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function userProgress()
    {
        return $this->hasMany(UserOnboardingProgress::class, 'step_id');
    }
}
