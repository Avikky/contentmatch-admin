<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlatformAnalytics extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'appserver';

    protected $fillable = [
        'total_users',
        'active_users',
        'total_communities',
        'total_content',
        'total_engagements',
        'platform_distribution',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'platform_distribution' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'total_users' => 'integer',
        'active_users' => 'integer',
        'total_communities' => 'integer',
        'total_content' => 'integer',
        'total_engagements' => 'integer',
    ];

    // Helper Methods
    public function getGrowthRate()
    {
        // Calculate growth rate between periods
        return [
            'users' => $this->calculateGrowthRate('total_users'),
            'communities' => $this->calculateGrowthRate('total_communities'),
            'content' => $this->calculateGrowthRate('total_content'),
            'engagements' => $this->calculateGrowthRate('total_engagements'),
        ];
    }

    private function calculateGrowthRate($metric)
    {
        $previous = self::where('period_end', '<', $this->period_start)
            ->orderBy('period_end', 'desc')
            ->first();

        if (! $previous || $previous->{$metric} == 0) {
            return 0;
        }

        return (($this->{$metric} - $previous->{$metric}) / $previous->{$metric}) * 100;
    }
}
