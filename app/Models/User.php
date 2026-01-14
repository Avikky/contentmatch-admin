<?php

namespace App\Models;

// use App\Http\Controllers\Gorse\GorseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use App\Models\Match;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    // Status constants
    const STATUS_ACTIVE = 'active';

    const STATUS_SUSPENDED = 'suspended';

    const STATUS_BANNED = 'banned';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $connection = 'appserver';

    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'bio',
        'profile_image_url',
        'banner_url',
        'dob',
        'gender',
        'status',
        'google_id',
        'discord_id',
        'onboarding_completed_at',
        'referral_code',
        'referred_by',
        'is_verified',
        'email_verified_at',
        'trial_ends_at',

        'should_show_suggested_account',
    ];

    protected $guards = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_active_at' => 'datetime',
        'onboarding_completed_at' => 'datetime',
        'dob' => 'date',
        'is_verified' => 'boolean',
    ];

    protected $with = ['preferences'];

    // Relationships
    // public function socialAccounts(): HasMany
    // {
    //     return $this->hasMany(UserSocialOAuth::class);
    // }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class, 'user_platform_links')
            ->withPivot(['platform_username', 'platform_user_id', 'platform_url', 'verified_at'])
            ->withTimestamps();
    }

    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(Interest::class, 'user_interests')
            ->withTimestamps();
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function getLastConversation($lastConversationId)
    {

        return Message::where('user_id', $this->id)
            ->where('conversation_id', $lastConversationId)
            ->latest()
            ->first();

    }

    public function supportiveCauses(): BelongsToMany
    {
        return $this->belongsToMany(SupportiveCause::class, 'user_supportive_causes')
            ->withTimestamps();
    }

    public function communities(): BelongsToMany
    {
        return $this->belongsToMany(Community::class, 'community_members')
            ->withPivot(['role', 'status', 'last_activity_at'])
            ->withTimestamps();
    }

    public function getFirstName()
    {
        $name = explode(' ', trim($this->full_name));

        return $name[0];
    }

    public function getLastName()
    {
        $name = explode(' ', trim($this->full_name));

        return $name[1] ?? '';
    }

    public function ownedCommunities(): HasMany
    {
        return $this->hasMany(Community::class, 'owner_id');
    }

    public function content(): HasMany
    {
        return $this->hasMany(Content::class);
    }

    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'blocks', 'blocker_id', 'blocked_id')
            ->withPivot(['reason'])
            ->withTimestamps();
    }

    // check if user is blocked
    public function isBlocked($userId): bool
    {
        return $this->blockedUsers()->where('blocked_id', $userId)->exists();
    }

    // public function engagements(): HasMany
    // {
    //     return $this->hasMany(Engagement::class);
    // }
    public function engagements(): HasMany
    {
        return $this->hasMany(Engagement::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }

    // public function deviceTokens(): HasMany
    // {
    //     return $this->hasMany(UserDeviceToken::class);
    // }

    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    // public function analytics(): HasMany
    // {
    //     return $this->hasMany(UserAnalytics::class);
    // }

    // public function matchPreferences(): HasOne
    // {
    //     return $this->hasOne(MatchPreference::class);
    // }

    // public function initiatedMatches(): HasMany
    // {
    //     return $this->hasMany(Match::class, 'initiator_id');
    // }

    // public function receivedMatches(): HasMany
    // {
    //     return $this->hasMany(Match::class, 'receiver_id');
    // }

    // public function collaborations(): HasMany
    // {
    //     return $this->hasMany(Collaboration::class, 'initiator_id')
    //         ->orWhere('receiver_id', $this->id);
    // }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot(['achieved_at', 'metadata'])
            ->withTimestamps();
    }

    public function languageSettings(): HasOne
    {
        return $this->hasOne(LanguageSettings::class);
    }

    public function notificationSettings(): HasOne
    {
        return $this->hasOne(NotificationSetting::class);
    }

    public function routeNotificationForFcm()
    {
        return $this->deviceTokens()
            ->where('is_active', true)
            ->pluck('device_token')
            ->toArray();
    }

    public function shouldReceiveNotification($type)
    {
        if (! $this->notificationSettings) {
            return true;
        }

        $preferences = $this->notificationSettings->preferences ?? [];

        return $preferences[$type] ?? true;
    }

    public function onboardingProgress()
    {
        return $this->hasMany(UserOnboardingProgress::class);
    }

    /**
     * Get conversations where the user is a participant
     */
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->withPivot(['last_read_at', 'status'])
            ->withTimestamps();
    }

    public function canAccessConversation($conversationId): bool
    {
        // check if conversation exist
        $conversation = Conversation::find($conversationId);
        if (! $conversation) {
            return false;
        }
        $conversationPaticipants = ConversationParticipant::where('conversation_id', $conversationId)
            ->get()->pluck('user_id')->toArray();

        if (! in_array($this->id, $conversationPaticipants)) {
            return false;
        }

        return true;
    }

    /**
     * Get all messages sent by the user
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'user_id');
    }

    /**
     * Get user's conversation participants records
     */
    public function conversationParticipants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    // public function hasCompletedOnboarding(): bool
    // {
    //     return !is_null($this->onboarding_completed_at);
    // }

    public function purposes(): BelongsToMany
    {
        return $this->belongsToMany(Purpose::class, 'user_purposes')
            ->withTimestamps();
    }

    public function audienceSizes(): BelongsToMany
    {
        return $this->belongsToMany(AudienceSize::class, 'user_audience_sizes')
            ->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'user_categories')
            ->withTimestamps();
    }

    public function markOnboardingCompleted(): void
    {
        $this->update([
            'onboarding_completed_at' => now(),
        ]);
    }

    // Users that this user is following
    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'followed_id')
            ->withTimestamps();
    }

    // Users that are following this user
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'followed_id', 'follower_id')
            ->withTimestamps();
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referredUsers()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    /**
     * Check if the authenticated user is following a specific user.
     *
     * @param  int  $userId  The ID of the user to check.
     */
    public function isFollowing($userId): bool
    {
        // Ensure the user is authenticated
        if (auth()->check()) {
            // Check if the authenticated user is following the given user
            return auth()->user()->following->contains($userId);
        }

        return false;
    }

    public function isMutualWith(int $userId): bool
    {
        return $this->following()->whereHas('following', function ($query) use ($userId) {
            $query->where('followers.follower_id', $userId);
        })->where('users.id', $userId)->exists();
    }

    /**
     * set trial period.
     *
     * @param  int  $userId  The ID of the user to follow.
     * @return bool
     */
    public function setTrialPeriod(): void
    {
        $trialDays = config('user.trial_days', 7);

        // trial period from the date of completed onboarding
        $trialPeriod = Carbon::parse($this->onboarding_completed_at)->addDays((int) $trialDays);

        $this->update([
            'trial_ends_at' => $trialPeriod,
        ]);
    }

    public function setEmailVerified(): void
    {
        $this->update([
            'email_verified_at' => now(),
        ]);
    }

    public function setVerified(): void
    {
        $this->update([
            'is_verified' => true,
        ]);
    }

    public function getAllCommunities()
    {
        // Fetch owned communities with member count
        $ownedCommunities = Community::where('owner_id', $this->id)
            ->withCount('members')
            ->inRandomOrder()
            ->limit(3)
            ->get();

        // Fetch joined communities with member count
        $joinedCommunities = $this->communities()
            ->withCount('members')
            ->inRandomOrder()
            ->limit(3)
            ->get();

        // Merge the two collections and remove duplicates
        $allCommunities = $ownedCommunities->merge($joinedCommunities)->unique('id');

        return $allCommunities;
    }

    public function getCommunityContentByInterest($limit = 5)
    {
        // Fetch user's interests and purposes
        $interests = $this->interests()->pluck('id');
        $purposes = $this->purposes()->pluck('id');

        // Fetch content that matches the user's interests and purposes
        $content = Content::whereHas('community', function ($query) use ($interests, $purposes) {
            $query->whereHas('interests', function ($q) use ($interests) {
                $q->whereIn('interests.id', $interests);
            })->orWhereHas('purposes', function ($q) use ($purposes) {
                $q->whereIn('purposes.id', $purposes);
            });
        })
            ->withCount(['likes', 'feedback', 'engagments']) // Add metrics for popularity
            ->orderByDesc('likes_count')
            ->orderByDesc('feedback_count')
            ->orderByDesc('engagements_count')
            ->limit($limit) // Limit the number of fallback results
            ->get();

        return $content;
    }

    public function isOnline()
    {
        return $this->last_active_at && $this->last_active_at > now()->subMinutes(10);
    }

    public function isModerator($communityId): bool
    {
        $community = Community::find($communityId);

        // check if user is a moderator in the community
        return $community->members()->where('user_id', $this->id)->where('role', 'moderator')->exists();
    }

    public function isAdmin($communityId): bool
    {

        $community = Community::find($communityId);

        // check if user is a moderator in the community
        return $community->members()->where('user_id', $this->id)->where('role', 'admin')->exists();

    }

    public function isMember($communityId): bool
    {
        $community = Community::find($communityId);

        return $community->members()->where('user_id', $this->id)->exists();
    }

    public function sentMessages()
    {
        return $this->hasMany(NewMessage::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(NewMessage::class, 'receiver_id');
    }

    public function canChatWith(User $user)
    {
        return $this->following->contains($user) || $user->following->contains($this);
    }

    public function hashtags()
    {
        return $this->morphToMany(Hashtag::class, 'hashtaggable');
    }

    //    public function addUserToGorse(){
    //         $merge = array_merge($this->purposes->pluck('name')->toArray(),$this->interests->pluck('name')->toArray());

    //         $payload = [
    //             "UserId" => "user_".$this->id,
    //             "Labels" => $merge,
    //             "Comment" => $this->full_name." account created"
    //         ];

    //         $gorseService = new GorseService();

    //         $gorseService->inserUserToGorse($payload);

    //    }

    public function sentRecommendations()
    {
        return $this->hasMany(SentRecommendation::class);
    }

    public function preferredLocale(): string
    {
        return $this->languageSettings->locale ?? 'en';
    }

    public function communityMessages()
    {
        return $this->hasMany(CommunityMessage::class);
    }

    // Admin-specific relationships
    public function moderationLogs(): HasMany
    {
        return $this->hasMany(UserModerationLog::class);
    }

    public function receivedReports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function submittedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }

    public function recentActivities()
    {
        return $this->hasMany(UserActivity::class)->latest()->limit(50);
    }

    public function analytics()
    {
        return $this->hasMany(UserAnalytics::class);
    }

    public function blockedByUsers()
    {
        return $this->belongsToMany(User::class, 'blocks', 'blocked_id', 'blocker_id')
            ->withPivot(['reason'])
            ->withTimestamps();
    }

    public function allSubscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('stripe_status', 'active')
            ->latest();
    }

    // Scopes
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', self::STATUS_BANNED);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeOnboardingCompleted($query)
    {
        return $query->whereNotNull('onboarding_completed_at');
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    public function isBanned(): bool
    {
        return $this->status === self::STATUS_BANNED;
    }

    public function isPremium(): bool
    {
        return $this->is_premium === true;
    }

    public function hasCompletedOnboarding(): bool
    {
        return ! is_null($this->onboarding_completed_at);
    }
}
