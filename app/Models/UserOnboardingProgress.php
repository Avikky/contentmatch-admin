<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOnboardingProgress extends Model
{
    use HasFactory;

    protected $connection = 'appserver';

    protected $fillable = [
        'user_id',
        'step_id',
        'data',
        'completed_at',
    ];

    protected $casts = [
        'data' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function step()
    {
        return $this->belongsTo(OnboardingStep::class, 'step_id');
    }
}
