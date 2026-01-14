<template>
  <AuthenticatedLayout>
    <div class="space-y-4 sm:space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">User Management</h1>
          <p class="mt-1 text-xs sm:text-sm text-slate-600">Manage platform users, moderation, and activities</p>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 gap-3 sm:gap-4 lg:gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
              <p class="text-xs sm:text-sm font-medium text-slate-600">Total Users</p>
              <p class="mt-1.5 sm:mt-2 text-2xl sm:text-3xl font-semibold text-slate-900">{{ statistics.total?.toLocaleString() || 0 }}</p>
            </div>
            <div class="rounded-full bg-blue-100 p-2.5 sm:p-3 flex-shrink-0">
              <svg class="h-5 w-5 sm:h-6 sm:w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
          </div>
          <p class="mt-2 sm:mt-3 text-xs text-slate-500">
            <span class="font-medium text-green-600">+{{ statistics.new_this_week || 0 }}</span> this week
          </p>
        </div>

        <div class="rounded-xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
              <p class="text-xs sm:text-sm font-medium text-slate-600">Active Users</p>
              <p class="mt-1.5 sm:mt-2 text-2xl sm:text-3xl font-semibold text-green-600">{{ statistics.active?.toLocaleString() || 0 }}</p>
            </div>
            <div class="rounded-full bg-green-100 p-2.5 sm:p-3 flex-shrink-0">
              <svg class="h-5 w-5 sm:h-6 sm:w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
              <p class="text-xs sm:text-sm font-medium text-slate-600">Suspended</p>
              <p class="mt-1.5 sm:mt-2 text-2xl sm:text-3xl font-semibold text-orange-600">{{ statistics.suspended?.toLocaleString() || 0 }}</p>
            </div>
            <div class="rounded-full bg-orange-100 p-2.5 sm:p-3 flex-shrink-0">
              <svg class="h-5 w-5 sm:h-6 sm:w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
              <p class="text-xs sm:text-sm font-medium text-slate-600">Banned</p>
              <p class="mt-1.5 sm:mt-2 text-2xl sm:text-3xl font-semibold text-red-600">{{ statistics.banned?.toLocaleString() || 0 }}</p>
            </div>
            <div class="rounded-full bg-red-100 p-2.5 sm:p-3 flex-shrink-0">
              <svg class="h-5 w-5 sm:h-6 sm:w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters and Search -->
      <div class="rounded-xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200">
        <form @submit.prevent="applyFilters" class="space-y-3 sm:space-y-0 sm:grid sm:grid-cols-2 lg:grid-cols-4 sm:gap-3 lg:gap-4">
          <div>
            <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Search</label>
            <input
              v-model="form.search"
              type="text"
              placeholder="Name, email, username..."
              class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
            />
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Status</label>
            <select
              v-model="form.status"
              class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
            >
              <option value="">All Statuses</option>
              <option value="active">Active</option>
              <option value="suspended">Suspended</option>
              <option value="banned">Banned</option>
            </select>
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Premium</label>
            <select
              v-model="form.is_premium"
              class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
            >
              <option value="">All Users</option>
              <option value="1">Premium Only</option>
              <option value="0">Free Only</option>
            </select>
          </div>

          <div class="flex items-end gap-2">
            <button
              type="submit"
              class="flex-1 rounded-lg bg-slate-900 px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-white hover:bg-slate-800"
            >
              Apply Filters
            </button>
            <button
              type="button"
              @click="resetFilters"
              class="rounded-lg border border-slate-300 bg-white px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
              Reset
            </button>
          </div>
        </form>
      </div>

      <!-- Users Table (Desktop) -->
      <div class="hidden lg:block rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">User</th>
                <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Status</th>
                <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Communities</th>
                <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Reports</th>
                <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Joined</th>
                <th class="px-4 xl:px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
              <tr v-for="user in users.data" :key="user.id" class="hover:bg-slate-50">
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4">
                  <div class="flex items-center gap-3">
                    <div class="h-9 w-9 xl:h-10 xl:w-10 flex-shrink-0">
                      <div class="flex h-9 w-9 xl:h-10 xl:w-10 items-center justify-center rounded-full bg-slate-900 text-xs xl:text-sm font-semibold text-white">
                        {{ getUserInitials(user) }}
                      </div>
                    </div>
                    <div class="min-w-0">
                      <div class="font-medium text-slate-900 text-sm truncate">{{ user.full_name || user.username }}</div>
                      <div class="text-xs sm:text-sm text-slate-500 truncate">{{ user.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4">
                  <span
                    :class="{
                      'bg-green-100 text-green-800': user.status === 'active',
                      'bg-orange-100 text-orange-800': user.status === 'suspended',
                      'bg-red-100 text-red-800': user.status === 'banned',
                    }"
                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                  >
                    {{ user.status }}
                  </span>
                  <span v-if="user.is_premium" class="ml-1.5 inline-flex rounded-full bg-purple-100 px-2 py-1 text-xs font-semibold text-purple-800">
                    Premium
                  </span>
                </td>
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4 text-sm text-slate-900">
                  {{ user.communities_count || 0 }}
                </td>
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4 text-sm text-slate-900">
                  {{ user.received_reports_count || 0 }}
                </td>
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4 text-sm text-slate-500">
                  {{ formatDate(user.created_at) }}
                </td>
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4 text-right text-sm font-medium">
                  <Link
                    :href="route('admin.users.show', user.id)"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    View Details
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="users.data.length > 0" class="border-t border-slate-200 bg-white px-4 py-3 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="text-xs sm:text-sm text-slate-700">
              Showing {{ users.from }} to {{ users.to }} of {{ users.total }} results
            </div>
            <div class="flex gap-1.5 sm:gap-2 flex-wrap">
              <Link
                v-for="(link, index) in users.links"
                :key="index"
                :href="link.url"
                :class="{
                  'bg-slate-900 text-white': link.active,
                  'bg-white text-slate-700 hover:bg-slate-50': !link.active,
                  'cursor-not-allowed opacity-50': !link.url,
                }"
                class="rounded-lg border border-slate-300 px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium"
                v-html="link.label"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Users Cards (Mobile/Tablet) -->
      <div class="lg:hidden space-y-3 sm:space-y-4">
        <div
          v-for="user in users.data"
          :key="user.id"
          class="rounded-xl bg-white p-4 shadow-sm border border-slate-200"
        >
          <div class="flex items-start justify-between gap-3 mb-3">
            <div class="flex items-center gap-3 min-w-0 flex-1">
              <div class="h-10 w-10 sm:h-12 sm:w-12 flex-shrink-0">
                <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
                  {{ getUserInitials(user) }}
                </div>
              </div>
              <div class="min-w-0">
                <div class="font-medium text-slate-900 text-sm sm:text-base truncate">{{ user.full_name || user.username }}</div>
                <div class="text-xs sm:text-sm text-slate-500 truncate">{{ user.email }}</div>
              </div>
            </div>
            <Link
              :href="route('admin.users.show', user.id)"
              class="flex-shrink-0 text-xs sm:text-sm text-blue-600 hover:text-blue-900 font-medium"
            >
              View
            </Link>
          </div>
          
          <div class="flex flex-wrap gap-2 mb-3">
            <span
              :class="{
                'bg-green-100 text-green-800': user.status === 'active',
                'bg-orange-100 text-orange-800': user.status === 'suspended',
                'bg-red-100 text-red-800': user.status === 'banned',
              }"
              class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
            >
              {{ user.status }}
            </span>
            <span v-if="user.is_premium" class="inline-flex rounded-full bg-purple-100 px-2.5 py-1 text-xs font-semibold text-purple-800">
              Premium
            </span>
          </div>

          <div class="grid grid-cols-3 gap-3 pt-3 border-t border-slate-100">
            <div>
              <p class="text-xs text-slate-500">Communities</p>
              <p class="text-sm font-semibold text-slate-900 mt-0.5">{{ user.communities_count || 0 }}</p>
            </div>
            <div>
              <p class="text-xs text-slate-500">Reports</p>
              <p class="text-sm font-semibold text-slate-900 mt-0.5">{{ user.received_reports_count || 0 }}</p>
            </div>
            <div>
              <p class="text-xs text-slate-500">Joined</p>
              <p class="text-xs sm:text-sm font-semibold text-slate-900 mt-0.5">{{ formatDate(user.created_at) }}</p>
            </div>
          </div>
        </div>

        <!-- Mobile Pagination -->
        <div v-if="users.data.length > 0" class="rounded-xl bg-white p-4 shadow-sm border border-slate-200">
          <div class="text-xs sm:text-sm text-slate-700 mb-3 text-center">
            Showing {{ users.from }} to {{ users.to }} of {{ users.total }} results
          </div>
          <div class="flex gap-1.5 flex-wrap justify-center">
            <Link
              v-for="(link, index) in users.links"
              :key="index"
              :href="link.url"
              :class="{
                'bg-slate-900 text-white': link.active,
                'bg-white text-slate-700 hover:bg-slate-50': !link.active,
                'cursor-not-allowed opacity-50': !link.url,
              }"
              class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs font-medium"
              v-html="link.label"
            />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  users: Object,
  statistics: Object,
  filters: Object,
});

const form = reactive({
  search: props.filters.search || '',
  status: props.filters.status || '',
  is_premium: props.filters.is_premium || '',
});

const applyFilters = () => {
  router.get(route('admin.users.index'), form, {
    preserveState: true,
    preserveScroll: true,
  });
};

const resetFilters = () => {
  form.search = '';
  form.status = '';
  form.is_premium = '';
  applyFilters();
};

const getUserInitials = (user) => {
  const name = user.full_name || user.username || user.email;
  return name
    .split(' ')
    .map((n) => n[0])
    .join('')
    .substring(0, 2)
    .toUpperCase();
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};
</script>
