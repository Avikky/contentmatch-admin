<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'appserver';

    protected $fillable = [
        'user_id',
        'content_id',
        'rating',
        'liked_aspects',
        'improvement_aspects',
        'proof_image_url',
    ];

    protected $casts = [
        'rating' => 'integer',
        'liked_aspects' => 'array',
        'improvement_aspects' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
