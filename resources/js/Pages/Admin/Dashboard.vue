<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const page = usePage();

const admin = computed(() => page.props.admin ?? page.props.auth?.user ?? {});
const analytics = computed(() => page.props.analytics ?? {});

// Calculate percentages and trends
const getUserGrowthPercentage = computed(() => {
  const users = analytics.value.users;
  if (!users || !users.total) return 0;
  return ((users.new_this_week / users.total) * 100).toFixed(1);
});

const getCommunityGrowthPercentage = computed(() => {
  const communities = analytics.value.communities;
  if (!communities || !communities.total) return 0;
  return ((communities.new_this_week / communities.total) * 100).toFixed(1);
});

const getContentGrowthPercentage = computed(() => {
  const content = analytics.value.content;
  if (!content || !content.total) return 0;
  return ((content.new_this_week / content.total) * 100).toFixed(1);
});

const formatNumber = (num) => {
  if (!num) return '0';
  return num.toLocaleString();
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
  });
};

const isSuperAdmin = computed(() => admin.value?.role === 'superadmin');
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Dashboard" />

    <section class="space-y-4 sm:space-y-6">
      <!-- Welcome Header -->
      <div class="rounded-2xl sm:rounded-3xl bg-gradient-to-r from-[#14092A] to-[#2B0F4A] px-4 sm:px-6 lg:px-8 py-6 sm:py-8 lg:py-10 text-white shadow-xl">
        <p class="text-xs sm:text-sm uppercase tracking-wider sm:tracking-[0.3em] text-white/60">Welcome back</p>
        <h1 class="mt-3 sm:mt-4 text-2xl sm:text-3xl font-semibold">
          {{ admin?.name || admin?.display_name || 'ContentMatch Admin' }}
        </h1>
        <p class="mt-2 sm:mt-3 max-w-xl text-xs sm:text-sm text-white/70">
          Monitor platform health, track growth metrics, and manage your community ecosystem.
        </p>
      </div>

      <!-- Quick Stats Overview -->
      <div class="grid gap-3 sm:gap-4 lg:gap-5 grid-cols-2 lg:grid-cols-4">
        <!-- Users -->
        <Link 
          :href="route('admin.users.index')"
          class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200 hover:shadow-md transition-shadow group"
        >
          <div class="flex items-center justify-between mb-3">
            <div class="rounded-lg bg-blue-100 p-2 sm:p-2.5">
              <svg class="h-5 w-5 sm:h-6 sm:w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <span class="text-xs font-medium text-green-600">+{{ getUserGrowthPercentage }}%</span>
          </div>
          <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Users</p>
          <p class="mt-2 text-2xl sm:text-3xl font-semibold text-slate-900">{{ formatNumber(analytics.users?.total) }}</p>
          <p class="mt-1 text-xs text-slate-500">{{ formatNumber(analytics.users?.new_this_week) }} new this week</p>
        </Link>

        <!-- Communities -->
        <Link
          :href="route('admin.communities.index')"
          class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200 hover:shadow-md transition-shadow group"
        >
          <div class="flex items-center justify-between mb-3">
            <div class="rounded-lg bg-purple-100 p-2 sm:p-2.5">
              <svg class="h-5 w-5 sm:h-6 sm:w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <span class="text-xs font-medium text-green-600">+{{ getCommunityGrowthPercentage }}%</span>
          </div>
          <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Communities</p>
          <p class="mt-2 text-2xl sm:text-3xl font-semibold text-slate-900">{{ formatNumber(analytics.communities?.total) }}</p>
          <p class="mt-1 text-xs text-slate-500">{{ formatNumber(analytics.communities?.active) }} active</p>
        </Link>

        <!-- Content -->
        <Link
          :href="route('admin.content.index')"
          class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200 hover:shadow-md transition-shadow group"
        >
          <div class="flex items-center justify-between mb-3">
            <div class="rounded-lg bg-emerald-100 p-2 sm:p-2.5">
              <svg class="h-5 w-5 sm:h-6 sm:w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
              </svg>
            </div>
            <span class="text-xs font-medium text-green-600">+{{ getContentGrowthPercentage }}%</span>
          </div>
          <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Content Posts</p>
          <p class="mt-2 text-2xl sm:text-3xl font-semibold text-slate-900">{{ formatNumber(analytics.content?.total) }}</p>
          <p class="mt-1 text-xs text-slate-500">{{ formatNumber(analytics.content?.published) }} published</p>
        </Link>

        <!-- Admins (Only for Superadmin) -->
        <Link
          v-if="isSuperAdmin && analytics.admins"
          :href="route('admin.admins.index')"
          class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200 hover:shadow-md transition-shadow group"
        >
          <div class="flex items-center justify-between mb-3">
            <div class="rounded-lg bg-indigo-100 p-2 sm:p-2.5">
              <svg class="h-5 w-5 sm:h-6 sm:w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
              </svg>
            </div>
          </div>
          <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Administrators</p>
          <p class="mt-2 text-2xl sm:text-3xl font-semibold text-slate-900">{{ formatNumber(analytics.admins?.total) }}</p>
          <p class="mt-1 text-xs text-slate-500">{{ formatNumber(analytics.admins?.active) }} active</p>
        </Link>

        <!-- Reports (Shown if not superadmin or to fill the 4th spot) -->
        <Link
          v-if="!isSuperAdmin || !analytics.admins"
          :href="route('admin.reports.index')"
          class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200 hover:shadow-md transition-shadow group"
        >
          <div class="flex items-center justify-between mb-3">
            <div class="rounded-lg bg-orange-100 p-2 sm:p-2.5">
              <svg class="h-5 w-5 sm:h-6 sm:w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <span v-if="analytics.reports?.pending" class="text-xs font-medium text-orange-600">{{ formatNumber(analytics.reports.pending) }} pending</span>
          </div>
          <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Reports</p>
          <p class="mt-2 text-2xl sm:text-3xl font-semibold text-slate-900">{{ formatNumber(analytics.reports?.total) }}</p>
          <p class="mt-1 text-xs text-slate-500">{{ formatNumber(analytics.reports?.resolved_this_week) }} resolved this week</p>
        </Link>
      </div>

      <!-- Detailed Analytics Grid -->
      <div class="grid gap-4 sm:gap-5 lg:gap-6 grid-cols-1 lg:grid-cols-2 xl:grid-cols-3">
        <!-- User Analytics -->
        <div class="rounded-xl sm:rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 lg:p-6 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-base sm:text-lg font-semibold text-slate-900">User Insights</h2>
            <Link :href="route('admin.users.index')" class="text-xs font-medium text-blue-600 hover:text-blue-700">
              View all →
            </Link>
          </div>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">Active Users</span>
              <span class="text-sm font-semibold text-slate-900">{{ formatNumber(analytics.users?.active) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">Premium Members</span>
              <span class="text-sm font-semibold text-emerald-600">{{ formatNumber(analytics.users?.premium) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">New This Month</span>
              <span class="text-sm font-semibold text-blue-600">{{ formatNumber(analytics.users?.new_this_month) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">Suspended</span>
              <span class="text-sm font-semibold text-orange-600">{{ formatNumber(analytics.users?.suspended) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">Banned</span>
              <span class="text-sm font-semibold text-red-600">{{ formatNumber(analytics.users?.banned) }}</span>
            </div>
          </div>
        </div>

        <!-- Community Analytics -->
        <div class="rounded-xl sm:rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 lg:p-6 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-base sm:text-lg font-semibold text-slate-900">Community Stats</h2>
            <Link :href="route('admin.communities.index')" class="text-xs font-medium text-blue-600 hover:text-blue-700">
              View all →
            </Link>
          </div>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">Active Communities</span>
              <span class="text-sm font-semibold text-slate-900">{{ formatNumber(analytics.communities?.active) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">Public</span>
              <span class="text-sm font-semibold text-blue-600">{{ formatNumber(analytics.communities?.public) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">Private</span>
              <span class="text-sm font-semibold text-purple-600">{{ formatNumber(analytics.communities?.private) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">New This Week</span>
              <span class="text-sm font-semibold text-green-600">{{ formatNumber(analytics.communities?.new_this_week) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">New This Month</span>
              <span class="text-sm font-semibold text-emerald-600">{{ formatNumber(analytics.communities?.new_this_month) }}</span>
            </div>
          </div>
        </div>

        <!-- Content Analytics -->
        <div class="rounded-xl sm:rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 lg:p-6 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-base sm:text-lg font-semibold text-slate-900">Content Overview</h2>
            <Link :href="route('admin.content.index')" class="text-xs font-medium text-blue-600 hover:text-blue-700">
              View all →
            </Link>
          </div>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">Published</span>
              <span class="text-sm font-semibold text-slate-900">{{ formatNumber(analytics.content?.published) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">Pending Review</span>
              <span class="text-sm font-semibold text-orange-600">{{ formatNumber(analytics.content?.pending) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">Flagged</span>
              <span class="text-sm font-semibold text-red-600">{{ formatNumber(analytics.content?.flagged) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">New This Week</span>
              <span class="text-sm font-semibold text-green-600">{{ formatNumber(analytics.content?.new_this_week) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">New This Month</span>
              <span class="text-sm font-semibold text-blue-600">{{ formatNumber(analytics.content?.new_this_month) }}</span>
            </div>
          </div>
        </div>

        <!-- Admin Analytics (Superadmin Only) -->
        <div v-if="isSuperAdmin && analytics.admins" class="rounded-xl sm:rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 lg:p-6 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-base sm:text-lg font-semibold text-slate-900">Admin Team</h2>
            <Link :href="route('admin.admins.index')" class="text-xs font-medium text-blue-600 hover:text-blue-700">
              Manage →
            </Link>
          </div>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">Total Admins</span>
              <span class="text-sm font-semibold text-slate-900">{{ formatNumber(analytics.admins?.total) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">Active</span>
              <span class="text-sm font-semibold text-green-600">{{ formatNumber(analytics.admins?.active) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">Superadmins</span>
              <span class="text-sm font-semibold text-purple-600">{{ formatNumber(analytics.admins?.superadmins) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">Moderators</span>
              <span class="text-sm font-semibold text-blue-600">{{ formatNumber(analytics.admins?.moderators) }}</span>
            </div>
          </div>
        </div>

        <!-- Top Communities -->
        <div class="rounded-xl sm:rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 lg:p-6 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-base sm:text-lg font-semibold text-slate-900">Top Communities</h2>
            <Link :href="route('admin.communities.index')" class="text-xs font-medium text-blue-600 hover:text-blue-700">
              View all →
            </Link>
          </div>
          <div class="space-y-3">
            <div v-for="(community, index) in analytics.communities?.top_communities" :key="community.id" class="flex items-center gap-3">
              <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 text-xs font-bold text-purple-600 flex-shrink-0">
                {{ index + 1 }}
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-slate-900 truncate">{{ community.name }}</p>
                <p class="text-xs text-slate-500">{{ formatNumber(community.members_count) }} members</p>
              </div>
            </div>
            <div v-if="!analytics.communities?.top_communities?.length" class="text-sm text-slate-500 text-center py-4">
              No communities yet
            </div>
          </div>
        </div>

        <!-- Recent Reports -->
        <div class="rounded-xl sm:rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 lg:p-6 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-base sm:text-lg font-semibold text-slate-900">Recent Reports</h2>
            <Link :href="route('admin.reports.index')" class="text-xs font-medium text-blue-600 hover:text-blue-700">
              View all →
            </Link>
          </div>
          <div class="space-y-3">
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
              <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-slate-600 uppercase tracking-wide">Total Reports</p>
                <p class="text-lg font-semibold text-slate-900">{{ formatNumber(analytics.reports?.total) }}</p>
              </div>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
              <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-slate-600 uppercase tracking-wide">Pending Review</p>
                <p class="text-lg font-semibold text-orange-600">{{ formatNumber(analytics.reports?.pending) }}</p>
              </div>
            </div>
            <div class="flex items-center justify-between py-2">
              <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-slate-600 uppercase tracking-wide">Resolved This Week</p>
                <p class="text-lg font-semibold text-green-600">{{ formatNumber(analytics.reports?.resolved_this_week) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Items -->
      <div class="grid gap-4 sm:gap-5 lg:gap-6 grid-cols-1 lg:grid-cols-2">
        <div class="rounded-xl sm:rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 lg:p-6 shadow-sm">
          <h2 class="text-base sm:text-lg font-semibold text-slate-900 mb-4">Priority Actions</h2>
          <ul class="space-y-3 sm:space-y-4 text-sm">
            <li v-if="analytics.reports?.pending > 0" class="flex items-start gap-2 sm:gap-3">
              <span class="mt-1 h-2 w-2 sm:h-2.5 sm:w-2.5 rounded-full bg-red-400 flex-shrink-0"></span>
              <div class="min-w-0">
                <p class="font-medium text-slate-800 text-xs sm:text-sm">Review pending reports</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ formatNumber(analytics.reports.pending) }} reports awaiting moderation</p>
              </div>
            </li>
            <li v-if="analytics.content?.pending > 0" class="flex items-start gap-2 sm:gap-3">
              <span class="mt-1 h-2 w-2 sm:h-2.5 sm:w-2.5 rounded-full bg-orange-400 flex-shrink-0"></span>
              <div class="min-w-0">
                <p class="font-medium text-slate-800 text-xs sm:text-sm">Moderate pending content</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ formatNumber(analytics.content.pending) }} posts pending review</p>
              </div>
            </li>
            <li v-if="analytics.content?.flagged > 0" class="flex items-start gap-2 sm:gap-3">
              <span class="mt-1 h-2 w-2 sm:h-2.5 sm:w-2.5 rounded-full bg-yellow-400 flex-shrink-0"></span>
              <div class="min-w-0">
                <p class="font-medium text-slate-800 text-xs sm:text-sm">Review flagged content</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ formatNumber(analytics.content.flagged) }} flagged posts need attention</p>
              </div>
            </li>
            <li v-if="analytics.users?.suspended > 0" class="flex items-start gap-2 sm:gap-3">
              <span class="mt-1 h-2 w-2 sm:h-2.5 sm:w-2.5 rounded-full bg-amber-400 flex-shrink-0"></span>
              <div class="min-w-0">
                <p class="font-medium text-slate-800 text-xs sm:text-sm">Review suspended users</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ formatNumber(analytics.users.suspended) }} users currently suspended</p>
              </div>
            </li>
            <li v-if="!analytics.reports?.pending && !analytics.content?.pending && !analytics.content?.flagged" class="flex items-start gap-2 sm:gap-3">
              <span class="mt-1 h-2 w-2 sm:h-2.5 sm:w-2.5 rounded-full bg-green-400 flex-shrink-0"></span>
              <div class="min-w-0">
                <p class="font-medium text-slate-800 text-xs sm:text-sm">All caught up!</p>
                <p class="text-xs text-slate-500 mt-0.5">No urgent actions required at this time</p>
              </div>
            </li>
          </ul>
        </div>

        <div class="rounded-xl sm:rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 lg:p-6 shadow-sm">
          <h2 class="text-base sm:text-lg font-semibold text-slate-900 mb-4">Platform Health</h2>
          <div class="space-y-4">
            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs sm:text-sm font-medium text-slate-700">User Activity</span>
                <span class="text-xs font-semibold text-slate-900">{{ analytics.users?.total > 0 ? ((analytics.users?.active / analytics.users?.total) * 100).toFixed(0) : 0 }}%</span>
              </div>
              <div class="w-full bg-slate-200 rounded-full h-2">
                <div 
                  class="bg-blue-600 h-2 rounded-full transition-all"
                  :style="{ width: `${analytics.users?.total > 0 ? (analytics.users?.active / analytics.users?.total) * 100 : 0}%` }"
                ></div>
              </div>
            </div>
            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs sm:text-sm font-medium text-slate-700">Community Engagement</span>
                <span class="text-xs font-semibold text-slate-900">{{ analytics.communities?.total > 0 ? ((analytics.communities?.active / analytics.communities?.total) * 100).toFixed(0) : 0 }}%</span>
              </div>
              <div class="w-full bg-slate-200 rounded-full h-2">
                <div 
                  class="bg-purple-600 h-2 rounded-full transition-all"
                  :style="{ width: `${analytics.communities?.total > 0 ? (analytics.communities?.active / analytics.communities?.total) * 100 : 0}%` }"
                ></div>
              </div>
            </div>
            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs sm:text-sm font-medium text-slate-700">Content Quality</span>
                <span class="text-xs font-semibold text-slate-900">{{ analytics.content?.total > 0 ? ((analytics.content?.published / analytics.content?.total) * 100).toFixed(0) : 0 }}%</span>
              </div>
              <div class="w-full bg-slate-200 rounded-full h-2">
                <div 
                  class="bg-emerald-600 h-2 rounded-full transition-all"
                  :style="{ width: `${analytics.content?.total > 0 ? (analytics.content?.published / analytics.content?.total) * 100 : 0}%` }"
                ></div>
              </div>
            </div>
            <div v-if="isSuperAdmin && analytics.admins">
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs sm:text-sm font-medium text-slate-700">Admin Coverage</span>
                <span class="text-xs font-semibold text-slate-900">{{ analytics.admins?.total > 0 ? ((analytics.admins?.active / analytics.admins?.total) * 100).toFixed(0) : 0 }}%</span>
              </div>
              <div class="w-full bg-slate-200 rounded-full h-2">
                <div 
                  class="bg-indigo-600 h-2 rounded-full transition-all"
                  :style="{ width: `${analytics.admins?.total > 0 ? (analytics.admins?.active / analytics.admins?.total) * 100 : 0}%` }"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </AuthenticatedLayout>
</template>
