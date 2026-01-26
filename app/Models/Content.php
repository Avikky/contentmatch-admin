<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use App\Models\Traits\Reportable; // Trait not found, commented out for now

class Content extends Model
{
    use HasFactory, SoftDeletes; // Removed Reportable trait

    protected $connection = 'appserver';
    
    protected $table = 'content';

    protected $fillable = [
        'user_id',
        'community_id',
        'platform_id',
        'title',
        'slug',
        'video_id',
        'description',
        'platform_content_id',
        'platform_content_url',
        'allow_feedback',
        'allow_engagement',
        'status',
    ];

    protected $casts = [
        'allow_feedback' => 'boolean',
        'allow_engagement' => 'boolean',
        'status' => 'string',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    const STATUS_FLAGGED = 'flagged';

    protected $appends = ['IframeVideoUrl'];

    protected $with = ['user', 'metrics'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function metrics()
    {
        return $this->hasOne(ContentMetrics::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function hashtags()
    {
        return $this->morphToMany(Hashtag::class, 'hashtaggable');
    }

    public function engagements()
    {
        return $this->hasMany(Engagement::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

    public function interests()
    {
        return $this->morphToMany(Interest::class, 'interestable');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function isYoutubeShorts()
    {

        return $this->platform->name == 'youtube' && $this->is_youtube_shorts;
    }

    public function getIframeVideoUrlAttribute()
    {
        return $this->embedUrl();
    }

    public function embedUrl()
    {
        switch ($this->platform?->name ?? '') {
            case 'youtube':
                $embedUrl = 'https://www.youtube.com/embed/'.$this->video_id.'?autoplay=0&controls=0&modestbranding=0&showinfo=0&rel=0&fs=0&iv_load_policy=3&disablekb=1&enablejsapi=1';
                break;
            case 'tiktok':
                $embedUrl = 'https://www.tiktok.com/embed/'.$this->video_id;
                break;
            case 'instagram':
                $embedUrl = 'https://www.instagram.com/p/'.$this->video_id.'/embed';
                break;
            default:
                $embedUrl = '';
        }

        return $embedUrl;
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function isLiked()
    {
        $like = $this->likes()->where('user_id', auth()->id())->first();

        if ($like) {
            return true;
        }

        return false;
    }

    public function peopleWhoLiked($number = 6)
    {
        return $this->likes()->with('user')->latest()->take($number)->get()->pluck('user');
    }

    public function peopleWhoFeedbacked($number = 10)
    {
        return $this->feedback()->with('user')->latest()->take($number)->get()->pluck('user');
    }

    public function isFeedbacked()
    {
        $feedback = $this->feedback()->where('user_id', auth()->id())->first();

        if ($feedback) {
            return true;
        }

        return false;
    }

    public function isEngaged(int $userId)
    {
        $engagement = $this->engagements()->where('user_id', $userId)->first();

        if ($engagement) {
            return true;
        }

        return false;
    }

    public function getProofImages()
    {
        $engagement = $this->engagements->where('user_id', auth()->id())->first();

        if (! $engagement) {
            return null;
        }

        $imgages = $engagement->proof_images ? json_decode($engagement->proof_images) : [];

        return $imgages;
    }

    public function totalYoutubeCreators()
    {
        return $this->platform()->where('name', 'youtube')->count();
    }

    public function totalTiktokCreators()
    {
        return $this->platform()->where('name', 'tiktok')->count();
    }

    public function totalInstagramCreators()
    {
        return $this->platform()->where('name', 'instagram')->count();
    }

    public function totalCreators()
    {
        return $this->platform()->count();
    }

    // Scopes for admin filtering
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeFlagged($query)
    {
        return $query->where('status', self::STATUS_FLAGGED);
    }
}
