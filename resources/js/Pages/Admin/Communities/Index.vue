<template>
  <AuthenticatedLayout>
    <div class="space-y-4 sm:space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Communities</h1>
          <p class="mt-1 text-xs sm:text-sm text-slate-600">Manage all communities on the platform</p>
        </div>
      </div>

      <!-- Search and Filters -->
      <div class="rounded-xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200">
        <form @submit.prevent="search" class="flex flex-col sm:flex-row gap-3 sm:gap-4">
          <div class="flex-1">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search communities by name, category, or description..."
              class="w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900 text-sm"
            />
          </div>
          <button
            type="submit"
            class="rounded-lg bg-slate-900 px-4 sm:px-6 py-2 text-xs sm:text-sm font-semibold text-white hover:bg-slate-800 whitespace-nowrap"
          >
            Search
          </button>
          <button
            v-if="filters.search"
            type="button"
            @click="clearSearch"
            class="rounded-lg border border-slate-300 bg-white px-4 sm:px-6 py-2 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 whitespace-nowrap"
          >
            Clear
          </button>
        </form>
      </div>

      <!-- Communities Table (Desktop) -->
      <div class="hidden lg:block rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                  Community
                </th>
                <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                  Owner
                </th>
                <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                  Members
                </th>
                <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                  Category
                </th>
                <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                  Status
                </th>
                <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                  Visibility
                </th>
                <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                  Created
                </th>
                <th class="px-4 xl:px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
              <tr v-for="community in (communities?.data || [])" :key="community.id" class="hover:bg-slate-50">
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4">
                  <div class="flex items-center gap-3">
                    <div class="h-9 w-9 xl:h-10 xl:w-10 flex-shrink-0">
                      <img
                        v-if="community.avatar_path"
                        :src="community.avatar_path"
                        :alt="community.name"
                        class="h-9 w-9 xl:h-10 xl:w-10 rounded-full object-cover"
                      />
                      <div
                        v-else
                        class="flex h-9 w-9 xl:h-10 xl:w-10 items-center justify-center rounded-full bg-slate-900 text-xs font-semibold text-white"
                      >
                        {{ getCommunityInitials(community.name) }}
                      </div>
                    </div>
                    <div class="min-w-0">
                      <Link
                        :href="route('admin.communities.show', community.id)"
                        class="font-medium text-slate-900 hover:text-slate-700 text-sm truncate block"
                      >
                        {{ community.name }}
                      </Link>
                      <div class="text-xs sm:text-sm text-slate-500 truncate">{{ community.slug }}</div>
                    </div>
                  </div>
                </td>
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4">
                  <div v-if="community.owner">
                    <div class="font-medium text-slate-900 text-sm truncate">{{ community.owner.full_name || community.owner.username }}</div>
                  </div>
                  <div v-else class="text-xs sm:text-sm text-slate-500">No owner</div>
                </td>
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4 text-sm text-slate-900">
                  {{ community.members_count || 0 }}
                </td>
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4">
                  <span v-if="community.category" class="text-sm text-slate-900">{{ community.category.name }}</span>
                  <span v-else class="text-sm text-slate-500">-</span>
                </td>
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4">
                  <span
                    :class="{
                      'bg-green-100 text-green-800': community.status === 'active',
                      'bg-gray-100 text-gray-800': community.status === 'archived',
                    }"
                    class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                  >
                    {{ community.status }}
                  </span>
                </td>
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4">
                  <span
                    :class="{
                      'bg-blue-100 text-blue-800': community.type === 'public',
                      'bg-purple-100 text-purple-800': community.type === 'private',
                    }"
                    class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                  >
                    {{ community.type || 'public' }}
                  </span>
                </td>
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4 text-sm text-slate-500">
                  {{ formatDate(community.created_at) }}
                </td>
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4 text-right text-sm font-medium">
                  <Link
                    :href="route('admin.communities.show', community.id)"
                    class="text-slate-600 hover:text-slate-900"
                  >
                    View Details
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Empty State -->
          <div v-if="!communities?.data || communities.data.length === 0" class="p-8 sm:p-12 text-center">
            <div class="text-slate-400">
              <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <p class="mt-3 sm:mt-4 text-sm font-medium text-slate-900">No communities found</p>
              <p class="mt-1 text-xs sm:text-sm text-slate-500">
                {{ filters?.search ? 'Try adjusting your search' : 'Communities will appear here when created' }}
              </p>
            </div>
          </div>
        </div>

        <!-- Desktop Pagination -->
        <div v-if="communities?.data && communities.data.length > 0" class="border-t border-slate-200 bg-white px-4 sm:px-6 py-3 sm:py-4">
          <div class="flex items-center justify-between">
            <div class="text-xs sm:text-sm text-slate-700">
              Showing
              <span class="font-medium">{{ communities?.from || 0 }}</span>
              to
              <span class="font-medium">{{ communities?.to || 0 }}</span>
              of
              <span class="font-medium">{{ communities?.total || 0 }}</span>
              results
            </div>
            <div class="flex gap-1.5 sm:gap-2">
              <template v-for="(link, index) in (communities?.links || [])" :key="index">
                <Link
                  v-if="link.url"
                  :href="link.url"
                  :class="{
                    'bg-slate-900 text-white': link.active,
                    'text-slate-700 hover:bg-slate-50': !link.active,
                  }"
                  class="rounded-lg border border-slate-300 px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium"
                >
                  <span v-html="link.label" />
                </Link>
                <span
                  v-else
                  class="rounded-lg border border-slate-300 px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium opacity-50 cursor-not-allowed"
                >
                  <span v-html="link.label" />
                </span>
              </template>
            </div>
          </div>
        </div>
      </div>

      <!-- Communities Cards (Mobile/Tablet) -->
      <div class="lg:hidden space-y-3 sm:space-y-4">
        <div
          v-for="community in (communities?.data || [])"
          :key="community.id"
          class="rounded-xl bg-white p-4 shadow-sm border border-slate-200"
        >
          <div class="flex items-start gap-3 mb-3">
            <div class="h-12 w-12 sm:h-14 sm:w-14 flex-shrink-0">
              <img
                v-if="community.avatar_path"
                :src="community.avatar_path"
                :alt="community.name"
                class="h-12 w-12 sm:h-14 sm:w-14 rounded-full object-cover"
              />
              <div
                v-else
                class="flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white"
              >
                {{ getCommunityInitials(community.name) }}
              </div>
            </div>
            <div class="min-w-0 flex-1">
              <Link
                :href="route('admin.communities.show', community.id)"
                class="font-medium text-slate-900 hover:text-slate-700 text-sm sm:text-base block truncate"
              >
                {{ community.name }}
              </Link>
              <div class="text-xs sm:text-sm text-slate-500 truncate">{{ community.slug }}</div>
              <div class="mt-2 flex flex-wrap gap-1.5">
                <span
                  :class="{
                    'bg-green-100 text-green-800': community.status === 'active',
                    'bg-gray-100 text-gray-800': community.status === 'archived',
                  }"
                  class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                >
                  {{ community.status }}
                </span>
                <span
                  :class="{
                    'bg-blue-100 text-blue-800': community.type === 'public',
                    'bg-purple-100 text-purple-800': community.type === 'private',
                  }"
                  class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                >
                  {{ community.type || 'public' }}
                </span>
                <span v-if="community.category" class="inline-flex rounded-full bg-orange-100 px-2 py-0.5 text-xs font-semibold text-orange-800">
                  {{ community.category.name }}
                </span>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-100">
            <div>
              <p class="text-xs text-slate-500">Members</p>
              <p class="text-sm font-semibold text-slate-900 mt-0.5">{{ community.members_count || 0 }}</p>
            </div>
            <div>
              <p class="text-xs text-slate-500">Owner</p>
              <p class="text-sm font-semibold text-slate-900 mt-0.5 truncate">{{ community.owner?.full_name || community.owner?.username || 'No owner' }}</p>
            </div>
            <div>
              <p class="text-xs text-slate-500">Created</p>
              <p class="text-xs sm:text-sm font-semibold text-slate-900 mt-0.5">{{ formatDate(community.created_at) }}</p>
            </div>
            <div class="flex items-end justify-end">
              <Link
                :href="route('admin.communities.show', community.id)"
                class="text-xs sm:text-sm text-blue-600 hover:text-blue-900 font-medium"
              >
                View Details →
              </Link>
            </div>
          </div>
        </div>

        <!-- Empty State Mobile -->
        <div v-if="!communities?.data || communities.data.length === 0" class="rounded-xl bg-white p-8 sm:p-12 text-center shadow-sm border border-slate-200">
          <div class="text-slate-400">
            <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="mt-3 sm:mt-4 text-sm font-medium text-slate-900">No communities found</p>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
              {{ filters?.search ? 'Try adjusting your search' : 'Communities will appear here when created' }}
            </p>
          </div>
        </div>

        <!-- Mobile Pagination -->
        <div v-if="communities?.data && communities.data.length > 0" class="rounded-xl bg-white p-4 shadow-sm border border-slate-200">
          <div class="text-xs sm:text-sm text-slate-700 mb-3 text-center">
            Showing
            <span class="font-medium">{{ communities?.from || 0 }}</span>
            to
            <span class="font-medium">{{ communities?.to || 0 }}</span>
            of
            <span class="font-medium">{{ communities?.total || 0 }}</span>
            results
          </div>
          <div class="flex gap-1.5 flex-wrap justify-center">
            <template v-for="(link, index) in (communities?.links || [])" :key="index">
              <Link
                v-if="link.url"
                :href="link.url"
                :class="{
                  'bg-slate-900 text-white': link.active,
                  'text-slate-700 hover:bg-slate-50': !link.active,
                }"
                class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs font-medium"
              >
                <span v-html="link.label" />
              </Link>
              <span
                v-else
                class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs font-medium opacity-50 cursor-not-allowed"
              >
                <span v-html="link.label" />
              </span>
            </template>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  communities: {
    type: Object,
    default: () => ({ data: [], total: 0, from: 0, to: 0, links: [] })
  },
  filters: {
    type: Object,
    default: () => ({})
  },
});

const searchQuery = ref(props.filters?.search || '');

const getCommunityInitials = (name) => {
  if (!name) return '?';
  const parts = name.split(' ');
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase();
  }
  return name.substring(0, 2).toUpperCase();
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const search = () => {
  router.get(
    route('admin.communities.index'),
    { search: searchQuery.value },
    {
      preserveState: true,
      preserveScroll: true,
    }
  );
};

const clearSearch = () => {
  searchQuery.value = '';
  router.get(route('admin.communities.index'), {}, {
    preserveState: true,
    preserveScroll: true,
  });
};
</script>
