<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Engagement extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'appserver';

    protected $fillable = [
        'user_id',
        'content_id',
        'did_engage',
        'did_subscribe',
        'did_like',
        'did_comment',
        'additional_comments',
        'proof_image',
    ];

    protected $casts = [
        'did_engage' => 'boolean',
        'did_subscribe' => 'boolean',
        'did_like' => 'boolean',
        'did_comment' => 'boolean',
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

    // sus
    public function messages()
    {
        return $this->morphMany(Message::class, 'messageable');
    }

    // protected function proofImages(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => !empty($value) ? unserialize($value) : '',
    //     );
    // }

}
