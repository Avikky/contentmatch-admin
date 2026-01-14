<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\EngagementMetric;
use App\Models\Hashtag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CommunitySeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::admins()->first();
        $member = User::nonAdmins()->first();

        if (! $owner) {
            return;
        }

        $communities = [
            [
                'name' => 'Creators Lounge',
                'category' => 'Creator Community',
                'description' => 'A space for emerging creators to share campaigns and feedback in real time.',
                'visibility' => 'public',
                'status' => 'active',
            ],
            [
                'name' => 'Brand Partnerships',
                'category' => 'Business',
                'description' => 'Match brands with the right creator communities for campaigns.',
                'visibility' => 'private',
                'status' => 'active',
            ],
            [
                'name' => 'Trend Watchers',
                'category' => 'Insights',
                'description' => 'Fast-moving community sharing real-time trend insights across platforms.',
                'visibility' => 'public',
                'status' => 'active',
            ],
        ];

        $hashtags = collect([
            'creatorlife',
            'contentmatch',
            'trendwatch',
            'brandcollab',
            'communityfirst',
        ])->map(fn ($tag) => Hashtag::firstOrCreate(['tag' => $tag])->id);

        foreach ($communities as $index => $data) {
            $community = Community::firstOrCreate(
                ['name' => $data['name']],
                [
                    'category' => $data['category'],
                    'description' => $data['description'],
                    'visibility' => $data['visibility'],
                    'status' => $data['status'],
                    'owner_id' => $owner->id,
                ]
            );

            $attachments = [$owner->id => ['role' => 'owner']];

            if ($member) {
                $attachments[$member->id] = ['role' => 'member'];
            }

            $community->members()->syncWithoutDetaching($attachments);

            $community->hashtags()->sync($hashtags->shuffle()->take(3));

            // Generate engagement metrics for past two weeks
            $start = Carbon::now()->subDays(13);
            for ($day = 0; $day < 14; $day++) {
                EngagementMetric::updateOrCreate(
                    [
                        'community_id' => $community->id,
                        'recorded_for' => $start->copy()->addDays($day)->toDateString(),
                    ],
                    [
                        'posts' => random_int(5, 25),
                        'comments' => random_int(10, 60),
                        'likes' => random_int(100, 450),
                        'shares' => random_int(10, 120),
                        'impressions' => random_int(1500, 9000),
                    ]
                );
            }
        }
    }
}
