<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'appserver';

    protected $fillable = [
        'reporter_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'description',
        'status',
        'resolved_by',
        'resolution_notes',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'status' => 'string',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_REVIEWING = 'reviewing';

    const STATUS_RESOLVED = 'resolved';

    const STATUS_DISMISSED = 'dismissed';

    // Reason constants
    const REASON_SPAM = 'spam';

    const REASON_HARASSMENT = 'harassment';

    const REASON_INAPPROPRIATE = 'inappropriate';

    const REASON_IMPERSONATION = 'impersonation';

    const REASON_COPYRIGHT = 'copyright';

    const REASON_OTHER = 'other';

    // Relationships
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeReviewing($query)
    {
        return $query->where('status', self::STATUS_REVIEWING);
    }

    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    public function scopeDismissed($query)
    {
        return $query->where('status', self::STATUS_DISMISSED);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('reportable_type', $type);
    }

    public function scopeByReason($query, $reason)
    {
        return $query->where('reason', $reason);
    }

    public function scopeByReporter($query, $userId)
    {
        return $query->where('reporter_id', $userId);
    }

    public function scopeByResolver($query, $userId)
    {
        return $query->where('resolved_by', $userId);
    }

    // Helper Methods
    public function markAsReviewing(?User $user = null)
    {
        $this->update([
            'status' => self::STATUS_REVIEWING,
            'resolved_by' => $user?->id,
        ]);

        // Dispatch notification job
        \App\Jobs\SendReportStatusNotification::dispatch($this->id, 'reviewing');
    }

    public function resolve(User $resolver, ?string $notes = null)
    {
        $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolved_by' => $resolver->id,
            'resolution_notes' => $notes,
            'resolved_at' => now(),
        ]);

        // Dispatch notification job
        \App\Jobs\SendReportStatusNotification::dispatch($this->id, 'resolved');
    }

    public function dismiss(User $resolver, ?string $notes = null)
    {
        $this->update([
            'status' => self::STATUS_DISMISSED,
            'resolved_by' => $resolver->id,
            'resolution_notes' => $notes,
            'resolved_at' => now(),
        ]);

        // Dispatch notification job
        \App\Jobs\SendReportStatusNotification::dispatch($this->id, 'dismissed');
    }

    public function isResolved(): bool
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    public function isDismissed(): bool
    {
        return $this->status === self::STATUS_DISMISSED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isReviewing(): bool
    {
        return $this->status === self::STATUS_REVIEWING;
    }

    // Events
    protected static function booted()
    {
        static::created(function ($report) {
            // Could trigger notifications
            // Could update reportable item status
            // Could notify admins/moderators
        });

        static::updated(function ($report) {
            if ($report->isDirty('status')) {
                // Could trigger status change notifications
                // Could update reportable item status
            }
        });
    }
}
