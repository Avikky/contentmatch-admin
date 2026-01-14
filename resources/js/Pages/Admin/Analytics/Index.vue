<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-slate-900">Analytics Dashboard</h1>
          <p class="mt-1 text-sm text-slate-600">Platform statistics and insights</p>
        </div>

        <div>
          <select
            v-model="selectedRange"
            @change="updateRange"
            class="rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
          >
            <option value="7">Last 7 days</option>
            <option value="14">Last 14 days</option>
            <option value="30">Last 30 days</option>
            <option value="90">Last 90 days</option>
          </select>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">Total Users</p>
              <p class="mt-2 text-3xl font-semibold text-slate-900">{{ summary.total_users?.toLocaleString() || 0 }}</p>
            </div>
            <div class="rounded-full bg-blue-100 p-3">
              <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
          </div>
          <p class="mt-3 text-xs text-slate-500">
            <span class="font-medium text-green-600">+{{ summary.new_users_this_week || 0 }}</span> this week
          </p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">Active Users</p>
              <p class="mt-2 text-3xl font-semibold text-green-600">{{ summary.active_users?.toLocaleString() || 0 }}</p>
            </div>
            <div class="rounded-full bg-green-100 p-3">
              <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">Communities</p>
              <p class="mt-2 text-3xl font-semibold text-purple-600">{{ summary.total_communities?.toLocaleString() || 0 }}</p>
            </div>
            <div class="rounded-full bg-purple-100 p-3">
              <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
          </div>
          <p class="mt-3 text-xs text-slate-500">
            <span class="font-medium">{{ summary.active_communities || 0 }}</span> active
          </p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">Content</p>
              <p class="mt-2 text-3xl font-semibold text-indigo-600">{{ summary.total_content?.toLocaleString() || 0 }}</p>
            </div>
            <div class="rounded-full bg-indigo-100 p-3">
              <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
            </div>
          </div>
          <p class="mt-3 text-xs text-slate-500">
            <span class="font-medium text-green-600">+{{ summary.new_content_this_week || 0 }}</span> this week
          </p>
        </div>
      </div>

      <!-- Reports Stats -->
      <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Report Statistics</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div class="text-center">
            <p class="text-3xl font-semibold text-orange-600">{{ reportStats.pending || 0 }}</p>
            <p class="mt-1 text-sm text-slate-600">Pending Reports</p>
          </div>
          <div class="text-center">
            <p class="text-3xl font-semibold text-blue-600">{{ reportStats.reviewing || 0 }}</p>
            <p class="mt-1 text-sm text-slate-600">Under Review</p>
          </div>
          <div class="text-center">
            <p class="text-3xl font-semibold text-green-600">{{ reportStats.resolved_this_week || 0 }}</p>
            <p class="mt-1 text-sm text-slate-600">Resolved This Week</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- User Growth Chart -->
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <h2 class="text-lg font-semibold text-slate-900 mb-4">User Growth</h2>
          <div class="h-64 flex items-center justify-center text-slate-500">
            <div class="text-center">
              <p class="text-sm">{{ userGrowth.length }} data points</p>
              <p class="text-xs mt-1">Total registrations in period</p>
            </div>
          </div>
        </div>

        <!-- Top Communities -->
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <h2 class="text-lg font-semibold text-slate-900 mb-4">Top Communities</h2>
          <div class="space-y-3">
            <div v-for="community in communityStats" :key="community.id" 
              class="flex items-center justify-between p-3 rounded-lg bg-slate-50 hover:bg-slate-100">
              <div class="flex-1">
                <p class="text-sm font-medium text-slate-900">{{ community.name }}</p>
                <p class="text-xs text-slate-500">Owner: {{ community.owner }}</p>
              </div>
              <div class="text-right">
                <p class="text-sm font-semibold text-slate-900">{{ community.members.toLocaleString() }}</p>
                <p class="text-xs text-slate-500">members</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Hashtags -->
      <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Most Used Hashtags</h2>
        <div class="flex flex-wrap gap-3">
          <div v-for="hashtag in topHashtags" :key="hashtag.id"
            class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-4 py-2">
            <span class="text-sm font-medium text-blue-800">#{{ hashtag.tag }}</span>
            <span class="rounded-full bg-blue-200 px-2 py-0.5 text-xs font-semibold text-blue-900">
              {{ hashtag.usage_count }}
            </span>
          </div>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <Link href="/admin/analytics/users" 
          class="rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-6 shadow-sm hover:shadow-md transition-shadow">
          <h3 class="text-lg font-semibold text-white">User Analytics</h3>
          <p class="mt-2 text-sm text-blue-100">Detailed user statistics and trends</p>
        </Link>

        <Link href="/admin/analytics/communities"
          class="rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 p-6 shadow-sm hover:shadow-md transition-shadow">
          <h3 class="text-lg font-semibold text-white">Community Analytics</h3>
          <p class="mt-2 text-sm text-purple-100">Community growth and engagement</p>
        </Link>

        <Link href="/admin/analytics/content"
          class="rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 p-6 shadow-sm hover:shadow-md transition-shadow">
          <h3 class="text-lg font-semibold text-white">Content Analytics</h3>
          <p class="mt-2 text-sm text-indigo-100">Content creation and performance</p>
        </Link>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  summary: Object,
  userGrowth: Array,
  communityStats: Array,
  topHashtags: Array,
  reportStats: Object,
  filters: Object,
});

const selectedRange = ref(props.filters?.range || 30);

const updateRange = () => {
  router.get('/admin/analytics', { range: selectedRange.value }, {
    preserveState: true,
    preserveScroll: true,
  });
};
</script>
