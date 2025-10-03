<template>
  <AuthenticatedLayout>
    <Head title="ContentMatch Dashboard" />

    <div class="space-y-8">
      <section class="rounded-3xl bg-[#0F051D] text-white px-6 py-8 md:px-10 md:py-10 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
          <div>
            <p class="text-sm uppercase tracking-widest text-white/60">ContentMatch Control Center</p>
            <h1 class="mt-3 text-3xl font-bold md:text-4xl">
              Hi {{ userName }}, let's orchestrate your communities.
            </h1>
            <p class="mt-4 max-w-2xl text-white/70">
              Keep an eye on creator growth, the pulse of your communities, and the hashtags sparking conversations across the platform.
            </p>
          </div>
          <div class="shrink-0 rounded-2xl bg-white/10 px-6 py-5 text-sm">
            <p class="text-white/60">Last sync</p>
            <p class="mt-2 text-xl font-semibold">{{ lastSync }}</p>
            <p class="mt-1 text-white/50">Engagement data updates nightly at 02:00 WAT.</p>
          </div>
        </div>
      </section>

      <section class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div
          v-for="card in metricCards"
          :key="card.title"
          class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
        >
          <div class="flex items-center justify-between text-xs uppercase tracking-widest text-slate-400">
            <span>{{ card.title }}</span>
            <span
              v-if="card.chip"
              class="rounded-full bg-[#F4F0FF] px-2 py-1 text-[10px] font-semibold text-[#4F2DBE]"
            >
              {{ card.chip }}
            </span>
          </div>
          <p class="mt-4 text-3xl font-bold text-[#0F051D]">{{ card.value }}</p>
          <p class="mt-3 text-sm text-slate-500">{{ card.subtitle }}</p>
        </div>
      </section>

      <section class="grid gap-6 xl:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
          <header class="flex items-start justify-between gap-4">
            <div>
              <h2 class="text-lg font-semibold text-slate-800">Engagement pulse</h2>
              <p class="text-sm text-slate-500">Posts, likes, comments, and shares over the last 30 days.</p>
            </div>
            <span class="rounded-full bg-[#F4F0FF] px-3 py-1 text-xs font-semibold text-[#4F2DBE]">{{ engagement.summary.posts }} posts tracked</span>
          </header>

          <div class="mt-8">
            <div class="grid gap-6 md:grid-cols-4">
              <div
                v-for="pill in statPills"
                :key="pill.label"
                class="rounded-2xl border border-slate-100 bg-[#F9F7FF] px-4 py-3"
              >
                <div class="flex items-center justify-between text-xs uppercase tracking-widest text-[#4F2DBE]">
                  <span>{{ pill.label }}</span>
                  <svg class="h-4 w-4 text-[#4F2DBE]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 12h16m-8-8v16" />
                  </svg>
                </div>
                <p class="mt-2 text-xl font-semibold text-[#0F051D]">{{ pill.value }}</p>
              </div>
            </div>

            <div class="mt-10">
              <div class="flex items-center justify-between text-xs text-slate-500">
                <span>30-day trend</span>
                <span>Daily totals</span>
              </div>
              <div class="mt-4 h-56">
                <div class="flex h-full items-end gap-2">
                  <div
                    v-for="day in engagementTrend"
                    :key="day.date"
                    class="group flex-1"
                  >
                    <div
                      class="rounded-t-lg bg-gradient-to-b from-[#6C4FE0] to-[#A68FFF]"
                      :style="{ height: `${day.height}%` }"
                    ></div>
                    <div class="mt-2 text-center text-[10px] text-slate-500">
                      {{ day.shortDate }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-6">
          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800">Trending hashtags</h2>
            <p class="mt-1 text-sm text-slate-500">Based on usage count platform-wide.</p>
            <ul class="mt-5 space-y-3">
              <li
                v-for="tag in trendingHashtags"
                :key="tag.id"
                class="flex items-center justify-between rounded-xl bg-[#F9F7FF] px-4 py-3 text-sm font-medium text-[#0F051D]"
              >
                <span>#{{ tag.tag }}</span>
                <span class="text-xs font-semibold text-[#4F2DBE]">{{ formatNumber(tag.usage_count) }} uses</span>
              </li>
              <li
                v-if="!trendingHashtags.length"
                class="rounded-xl border border-dashed border-slate-200 px-4 py-6 text-center text-sm text-slate-500"
              >
                No hashtag data yet. Seed some campaigns to populate insights.
              </li>
            </ul>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800">Newest admins</h2>
            <p class="mt-1 text-sm text-slate-500">Recent teammates with elevated access.</p>
            <ul class="mt-5 space-y-4">
              <li
                v-for="admin in recentAdmins"
                :key="admin.id"
                class="flex items-start justify-between"
              >
                <div>
                  <p class="text-sm font-semibold text-[#0F051D]">{{ admin.name }}</p>
                  <p class="text-xs text-slate-500">{{ admin.email }}</p>
                </div>
                <span class="rounded-full bg-[#F4F0FF] px-2.5 py-1 text-[11px] font-semibold text-[#4F2DBE]">
                  Joined {{ admin.joined_at ?? 'n/a' }}
                </span>
              </li>
              <li
                v-if="!recentAdmins.length"
                class="rounded-xl border border-dashed border-slate-200 px-4 py-6 text-center text-sm text-slate-500"
              >
                Invite your first admin to collaborate on moderation workflows.
              </li>
            </ul>
          </div>
        </div>
      </section>

      <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <header class="flex flex-wrap items-start justify-between gap-4">
          <div>
            <h2 class="text-lg font-semibold text-slate-800">Community spotlight</h2>
            <p class="text-sm text-slate-500">Top performers ranked by membership.</p>
          </div>
          <span class="rounded-full bg-[#0F051D] px-3 py-1 text-xs font-semibold text-white">{{ topCommunities.length }} tracked</span>
        </header>

        <div class="mt-6 overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
              <tr>
                <th class="py-3">Community</th>
                <th class="py-3">Owner</th>
                <th class="py-3">Members</th>
                <th class="py-3">Visibility</th>
                <th class="py-3">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="community in topCommunities" :key="community.id" class="hover:bg-[#F9F7FF]">
                <td class="py-3 font-medium text-[#0F051D]">
                  {{ community.name }}
                  <p class="text-xs text-slate-500">/{{ community.slug }}</p>
                </td>
                <td class="py-3 text-slate-600">{{ community.owner }}</td>
                <td class="py-3 text-slate-600">{{ formatNumber(community.members) }}</td>
                <td class="py-3">
                  <span class="rounded-full bg-[#F4F0FF] px-2.5 py-0.5 text-[11px] font-semibold text-[#4F2DBE]">
                    {{ community.visibility }}
                  </span>
                </td>
                <td class="py-3">
                  <span
                    :class="[
                      'rounded-full px-2.5 py-0.5 text-[11px] font-semibold',
                      community.status === 'active'
                        ? 'bg-emerald-100 text-emerald-700'
                        : 'bg-amber-100 text-amber-700'
                    ]"
                  >
                    {{ community.status }}
                  </span>
                </td>
              </tr>
              <tr v-if="!topCommunities.length">
                <td colspan="5" class="py-6 text-center text-sm text-slate-500">
                  No communities available yet. Create one to begin tracking engagement.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const page = usePage();

const props = defineProps({
  totals: {
    type: Object,
    default: () => ({})
  },
  engagement: {
    type: Object,
    default: () => ({ summary: {}, trend: [] })
  },
  topCommunities: {
    type: Array,
    default: () => []
  },
  trendingHashtags: {
    type: Array,
    default: () => []
  },
  recentAdmins: {
    type: Array,
    default: () => []
  }
});

const formatNumber = (value) => new Intl.NumberFormat('en-US').format(value ?? 0);

const userName = computed(
  () => page.props.auth?.user?.display_name ?? page.props.auth?.user?.name ?? 'there'
);

const lastSync = computed(() => {
  const date = new Date();
  return date.toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit'
  });
});

const totals = computed(() => ({
  total_users: props.totals?.total_users ?? 0,
  administrators: props.totals?.administrators ?? 0,
  new_users_week: props.totals?.new_users_week ?? 0,
  active_communities: props.totals?.active_communities ?? 0,
  total_communities: props.totals?.total_communities ?? 0
}));

const metricCards = computed(() => [
  {
    title: 'Total users',
    value: formatNumber(totals.value.total_users),
    subtitle: 'Platform-wide'
  },
  {
    title: 'Administrators',
    value: formatNumber(totals.value.administrators),
    subtitle: 'Team members managing ops',
    chip: totals.value.new_users_week
      ? `+${formatNumber(totals.value.new_users_week)} new this week`
      : null
  },
  {
    title: 'Active communities',
    value: formatNumber(totals.value.active_communities),
    subtitle: 'Monitored spaces',
    chip: `${formatNumber(totals.value.total_communities)} total`
  },
  {
    title: '7-day impressions',
    value: formatNumber(props.engagement?.summary?.impressions ?? 0),
    subtitle: 'Signals captured'
  }
]);

const engagement = computed(() => ({
  summary: {
    posts: props.engagement?.summary?.posts ?? 0,
    comments: props.engagement?.summary?.comments ?? 0,
    likes: props.engagement?.summary?.likes ?? 0,
    shares: props.engagement?.summary?.shares ?? 0,
    impressions: props.engagement?.summary?.impressions ?? 0
  },
  trend: props.engagement?.trend ?? []
}));

const statPills = computed(() => [
  { label: 'Posts', value: formatNumber(engagement.value.summary.posts) },
  { label: 'Comments', value: formatNumber(engagement.value.summary.comments) },
  { label: 'Likes', value: formatNumber(engagement.value.summary.likes) },
  { label: 'Shares', value: formatNumber(engagement.value.summary.shares) }
]);

const trendMax = computed(() => {
  const values = engagement.value.trend.map(
    (day) => day.posts + day.likes + day.comments + day.shares
  );
  return values.length ? Math.max(...values) : 0;
});

const engagementTrend = computed(() =>
  engagement.value.trend.map((day) => {
    const total = day.posts + day.likes + day.comments + day.shares;
    const height = trendMax.value ? Math.max(8, Math.round((total / trendMax.value) * 100)) : 8;
    const dateObj = new Date(day.date);

    return {
      ...day,
      total,
      height,
      shortDate: dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
    };
  })
);

const trendingHashtags = computed(() => props.trendingHashtags ?? []);
const recentAdmins = computed(() => props.recentAdmins ?? []);
const topCommunities = computed(() => props.topCommunities ?? []);
</script>
