<template>
  <AuthenticatedLayout>
    <Head title="Content Analytics" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-slate-900">Content Analytics</h1>
          <p class="mt-1 text-sm text-slate-600">Insights, trends, and performance metrics</p>
        </div>
        <div class="flex gap-3">
          <select
            v-model="timeRange"
            @change="updateTimeRange"
            class="rounded-lg border-slate-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
          >
            <option value="7">Last 7 days</option>
            <option value="14">Last 14 days</option>
            <option value="30">Last 30 days</option>
            <option value="90">Last 90 days</option>
          </select>
          <Link
            :href="route('admin.content.index')"
            class="inline-flex items-center px-4 py-2 bg-slate-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition"
          >
            Back to Content
          </Link>
        </div>
      </div>

      <!-- Overview Statistics -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-6 text-white shadow-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-blue-100">Total Content</p>
              <p class="mt-2 text-3xl font-bold">{{ stats.total_content?.toLocaleString() || 0 }}</p>
              <p class="mt-2 text-xs text-blue-100">
                <span class="font-semibold">+{{ stats.recent_content || 0 }}</span> in period
              </p>
            </div>
            <div class="rounded-full bg-white/20 p-3">
              <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-gradient-to-br from-green-500 to-green-600 p-6 text-white shadow-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-green-100">Total Engagements</p>
              <p class="mt-2 text-3xl font-bold">{{ stats.total_engagements?.toLocaleString() || 0 }}</p>
              <p class="mt-2 text-xs text-green-100">
                <span class="font-semibold">{{ stats.avg_engagements_per_content || 0 }}</span> avg per content
              </p>
            </div>
            <div class="rounded-full bg-white/20 p-3">
              <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-gradient-to-br from-yellow-500 to-yellow-600 p-6 text-white shadow-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-yellow-100">Average Rating</p>
              <p class="mt-2 text-3xl font-bold">{{ stats.avg_rating || '0.0' }}</p>
              <p class="mt-2 text-xs text-yellow-100">
                <span class="font-semibold">{{ stats.total_feedback || 0 }}</span> total reviews
              </p>
            </div>
            <div class="rounded-full bg-white/20 p-3">
              <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 p-6 text-white shadow-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-purple-100">Active Creators</p>
              <p class="mt-2 text-3xl font-bold">{{ stats.active_creators?.toLocaleString() || 0 }}</p>
              <p class="mt-2 text-xs text-purple-100">
                <span class="font-semibold">{{ stats.active_engagers || 0 }}</span> active engagers
              </p>
            </div>
            <div class="rounded-full bg-white/20 p-3">
              <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Content Trends Chart -->
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <h3 class="text-lg font-semibold text-slate-900 mb-4">Content Creation Trends</h3>
          <div class="h-64">
            <canvas ref="contentChart"></canvas>
          </div>
        </div>

        <!-- Engagement Trends Chart -->
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <h3 class="text-lg font-semibold text-slate-900 mb-4">Engagement Trends</h3>
          <div class="h-64">
            <canvas ref="engagementChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Platform Distribution -->
      <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
        <h3 class="text-lg font-semibold text-slate-900 mb-4">Platform Distribution</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
          <div
            v-for="platform in platformStats"
            :key="platform.name"
            class="flex flex-col items-center p-4 rounded-lg bg-slate-50 hover:bg-slate-100 transition"
          >
            <div class="text-3xl font-bold text-slate-900">{{ platform.count }}</div>
            <div class="text-sm text-slate-600 mt-1">{{ platform.name }}</div>
            <div class="text-xs text-slate-400 mt-1">{{ platform.percentage }}%</div>
          </div>
        </div>
      </div>

      <!-- Trending Section -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Trending Hashtags -->
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <h3 class="text-lg font-semibold text-slate-900 mb-4">Trending Hashtags</h3>
          <div class="space-y-3">
            <div
              v-for="(hashtag, index) in trendingHashtags"
              :key="hashtag.id"
              class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 transition"
            >
              <div class="flex items-center space-x-3">
                <div
                  :class="{
                    'bg-yellow-100 text-yellow-800': index === 0,
                    'bg-slate-100 text-slate-600': index === 1,
                    'bg-orange-100 text-orange-600': index === 2,
                    'bg-blue-100 text-blue-600': index > 2
                  }"
                  class="flex items-center justify-center w-8 h-8 rounded-full font-semibold text-sm"
                >
                  {{ index + 1 }}
                </div>
                <div>
                  <div class="font-medium text-slate-900">#{{ hashtag.name }}</div>
                  <div class="text-xs text-slate-500">{{ hashtag.usage_count }} uses</div>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <svg
                  v-if="hashtag.trending_up"
                  class="w-5 h-5 text-green-500"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <svg
                  v-else
                  class="w-5 h-5 text-red-500"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Categories -->
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <h3 class="text-lg font-semibold text-slate-900 mb-4">Top Categories</h3>
          <div class="space-y-3">
            <div
              v-for="category in topCategories"
              :key="category.id"
              class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 transition"
            >
              <div class="flex items-center space-x-3">
                <div class="w-2 h-12 rounded-full bg-gradient-to-b from-blue-500 to-purple-500"></div>
                <div>
                  <div class="font-medium text-slate-900">{{ category.name }}</div>
                  <div class="text-xs text-slate-500">{{ category.content_count }} content items</div>
                </div>
              </div>
              <div class="text-right">
                <div class="text-lg font-semibold text-slate-900">{{ category.engagement_count }}</div>
                <div class="text-xs text-slate-500">engagements</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Most Engaged Content -->
      <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
        <h3 class="text-lg font-semibold text-slate-900 mb-4">Most Engaged Content</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                  Rank
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                  Content
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                  Creator
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                  Engagements
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                  Likes
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                  Rating
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
                  Action
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
              <tr v-for="(content, index) in mostEngagedContent" :key="content.id" class="hover:bg-slate-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div
                    :class="{
                      'bg-yellow-100 text-yellow-800': index === 0,
                      'bg-slate-100 text-slate-600': index === 1,
                      'bg-orange-100 text-orange-600': index === 2,
                      'bg-blue-50 text-blue-600': index > 2
                    }"
                    class="flex items-center justify-center w-8 h-8 rounded-full font-semibold text-sm"
                  >
                    {{ index + 1 }}
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="h-12 w-16 flex-shrink-0 rounded bg-slate-100 overflow-hidden">
                      <img
                        v-if="content.thumbnail_url"
                        :src="content.thumbnail_url"
                        :alt="content.title"
                        class="h-full w-full object-cover"
                      />
                    </div>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-slate-900">{{ content.title || 'Untitled' }}</div>
                      <div class="text-xs text-slate-500">{{ content.platform?.name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-slate-900">{{ content.user?.full_name || content.user?.username }}</div>
                  <div class="text-xs text-slate-500">@{{ content.user?.username }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="text-sm font-semibold text-slate-900">{{ content.engagements_count }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <svg class="w-5 h-5 text-pink-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-semibold text-slate-900">{{ content.likes_count }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="text-sm font-semibold text-slate-900">{{ content.avg_rating || 'N/A' }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <Link
                    :href="route('admin.content.show', content.id)"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    View Details
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Leaderboards -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Top Creators -->
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <h3 class="text-lg font-semibold text-slate-900 mb-4">🏆 Top Content Creators</h3>
          <div class="space-y-3">
            <div
              v-for="(creator, index) in topCreators"
              :key="creator.id"
              class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 transition"
            >
              <div class="flex items-center space-x-3">
                <div
                  :class="{
                    'bg-gradient-to-br from-yellow-400 to-yellow-600': index === 0,
                    'bg-gradient-to-br from-slate-300 to-slate-400': index === 1,
                    'bg-gradient-to-br from-orange-400 to-orange-600': index === 2,
                    'bg-gradient-to-br from-blue-400 to-blue-500': index > 2
                  }"
                  class="flex items-center justify-center w-10 h-10 rounded-full text-white font-bold"
                >
                  {{ index + 1 }}
                </div>
                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-semibold">
                  {{ creator.username?.substring(0, 2).toUpperCase() }}
                </div>
                <div>
                  <div class="font-medium text-slate-900">{{ creator.full_name || creator.username }}</div>
                  <div class="text-xs text-slate-500">@{{ creator.username }}</div>
                </div>
              </div>
              <div class="text-right">
                <div class="text-lg font-semibold text-slate-900">{{ creator.content_count }}</div>
                <div class="text-xs text-slate-500">content items</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Engagers -->
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <h3 class="text-lg font-semibold text-slate-900 mb-4">⚡ Most Active Engagers</h3>
          <div class="space-y-3">
            <div
              v-for="(engager, index) in topEngagers"
              :key="engager.id"
              class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 transition"
            >
              <div class="flex items-center space-x-3">
                <div
                  :class="{
                    'bg-gradient-to-br from-yellow-400 to-yellow-600': index === 0,
                    'bg-gradient-to-br from-slate-300 to-slate-400': index === 1,
                    'bg-gradient-to-br from-orange-400 to-orange-600': index === 2,
                    'bg-gradient-to-br from-green-400 to-green-500': index > 2
                  }"
                  class="flex items-center justify-center w-10 h-10 rounded-full text-white font-bold"
                >
                  {{ index + 1 }}
                </div>
                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-green-500 to-teal-500 flex items-center justify-center text-white font-semibold">
                  {{ engager.username?.substring(0, 2).toUpperCase() }}
                </div>
                <div>
                  <div class="font-medium text-slate-900">{{ engager.full_name || engager.username }}</div>
                  <div class="text-xs text-slate-500">@{{ engager.username }}</div>
                </div>
              </div>
              <div class="text-right">
                <div class="text-lg font-semibold text-slate-900">{{ engager.engagement_count }}</div>
                <div class="text-xs text-slate-500">engagements</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Chart from 'chart.js/auto';

const props = defineProps({
  stats: Object,
  contentPerDay: Array,
  engagementPerDay: Array,
  platformStats: Array,
  trendingHashtags: Array,
  topCategories: Array,
  mostEngagedContent: Array,
  topCreators: Array,
  topEngagers: Array,
  days: {
    type: Number,
    default: 30,
  },
});

const timeRange = ref(props.days);
const contentChart = ref(null);
const engagementChart = ref(null);
let contentChartInstance = null;
let engagementChartInstance = null;

const updateTimeRange = () => {
  router.get(route('admin.content.analytics'), { days: timeRange.value }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const initContentChart = () => {
  if (contentChartInstance) {
    contentChartInstance.destroy();
  }

  const ctx = contentChart.value?.getContext('2d');
  if (!ctx) return;

  const labels = props.contentPerDay?.map(item => item.date) || [];
  const data = props.contentPerDay?.map(item => item.count) || [];

  contentChartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Content Created',
        data,
        borderColor: 'rgb(59, 130, 246)',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        tension: 0.4,
        fill: true,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          mode: 'index',
          intersect: false,
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0,
          }
        }
      }
    }
  });
};

const initEngagementChart = () => {
  if (engagementChartInstance) {
    engagementChartInstance.destroy();
  }

  const ctx = engagementChart.value?.getContext('2d');
  if (!ctx) return;

  const labels = props.engagementPerDay?.map(item => item.date) || [];
  const data = props.engagementPerDay?.map(item => item.count) || [];

  engagementChartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Engagements',
        data,
        borderColor: 'rgb(34, 197, 94)',
        backgroundColor: 'rgba(34, 197, 94, 0.1)',
        tension: 0.4,
        fill: true,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          mode: 'index',
          intersect: false,
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0,
          }
        }
      }
    }
  });
};

onMounted(() => {
  initContentChart();
  initEngagementChart();
});

watch(() => props.contentPerDay, () => {
  initContentChart();
});

watch(() => props.engagementPerDay, () => {
  initEngagementChart();
});
</script>
