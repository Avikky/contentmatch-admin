<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'appserver';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'content',
        'content_id',
        'status',
        'type',
        'attachments',
        'is_system_message',
        'edited_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_system_message' => 'boolean',
        'edited_at' => 'datetime',
    ];

    protected $appends = ['content_link'];

    protected $with = ['engagementContent'];

    const PENDING = 'pending';

    const ACCEPTED = 'accepted';

    const MESSAGE_TYPE = 'message';

    const ENGAGEMENT_TYPE = 'engagement_request';

    public function getContentLinkAttribute()
    {

        if ($this->type === self::ENGAGEMENT_TYPE && $this->content_id) {
            return url("/content/{$this->content_id}");
        }

        return null;
    }

    // Relationships
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {

        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // shoyuld have used morph
    public function engagementContent()
    {

        return $this->belongsTo(Content::class, 'content_id');
    }

    public function getConversationParticipantExcluding($userId = null)
    {
        $userId = $userId ?? $this->user_id;

        return $this->conversation
            ->participants()
            ->where('user_id', '!=', $userId);
    }

    public function didEngagedViaEngagemetRequest($contentId)
    {

        $participantId = $this->getConversationParticipantExcluding()->pluck('user_id')
            ->first();

        return Engagement::where('user_id', $participantId)
            ->where('content_id', $contentId)
            ->exists();
    }

    public function messageReceiver($userId = null)
    {
        return $this->getConversationParticipantExcluding($userId)->first();
    }
}
