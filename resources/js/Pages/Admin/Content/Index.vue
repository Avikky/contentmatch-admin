<template>
  <AuthenticatedLayout>
    <Head title="Content Management" />

    <div class="space-y-4 sm:space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Content Management</h1>
          <p class="mt-1 text-xs sm:text-sm text-slate-600">Monitor, moderate, and manage all platform content</p>
        </div>
        <div class="flex flex-wrap gap-2 sm:gap-3 w-full sm:w-auto">
          <Link
            :href="route('admin.content.analytics')"
            class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-purple-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition flex-1 sm:flex-none"
          >
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span class="hidden sm:inline">Analytics</span>
            <span class="sm:hidden">Stats</span>
          </Link>
          <Link
            :href="route('admin.content.reported')"
            class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition flex-1 sm:flex-none"
          >
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="hidden sm:inline">Reported Content</span>
            <span class="sm:hidden">Reports</span>
            <span v-if="statistics.reported_count > 0" class="ml-1.5 sm:ml-2 inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded text-xs font-medium bg-white text-red-600">
              {{ statistics.reported_count }}
            </span>
          </Link>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 gap-3 sm:gap-4 lg:gap-6 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
              <p class="text-xs sm:text-sm font-medium text-slate-600">Total Content</p>
              <p class="mt-1.5 sm:mt-2 text-2xl sm:text-3xl font-semibold text-slate-900">{{ statistics.total?.toLocaleString() || 0 }}</p>
            </div>
            <div class="rounded-full bg-blue-100 p-2.5 sm:p-3 flex-shrink-0">
              <svg class="h-5 w-5 sm:h-6 sm:w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
              </svg>
            </div>
          </div>
          <p class="mt-2 sm:mt-3 text-xs text-slate-500">
            <span class="font-medium text-green-600">+{{ statistics.new_today || 0 }}</span> today
          </p>
        </div>

        <div class="rounded-xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
              <p class="text-xs sm:text-sm font-medium text-slate-600">Published</p>
              <p class="mt-1.5 sm:mt-2 text-2xl sm:text-3xl font-semibold text-green-600">{{ statistics.published?.toLocaleString() || 0 }}</p>
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
              <p class="text-xs sm:text-sm font-medium text-slate-600">Reported</p>
              <p class="mt-1.5 sm:mt-2 text-2xl sm:text-3xl font-semibold text-orange-600">{{ statistics.reported_count?.toLocaleString() || 0 }}</p>
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
              <p class="text-xs sm:text-sm font-medium text-slate-600">Avg Rating</p>
              <p class="mt-1.5 sm:mt-2 text-2xl sm:text-3xl font-semibold text-purple-600">{{ statistics.avg_rating || '0.0' }}</p>
            </div>
            <div class="rounded-full bg-purple-100 p-2.5 sm:p-3 flex-shrink-0">
              <svg class="h-5 w-5 sm:h-6 sm:w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
              </svg>
            </div>
          </div>
          <p class="mt-2 sm:mt-3 text-xs text-slate-500">From {{ statistics.feedback_count || 0 }} reviews</p>
        </div>

        <div class="rounded-xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
              <p class="text-sm font-medium text-slate-600">Removed</p>
              <p class="mt-2 text-3xl font-semibold text-red-600">{{ statistics.removed?.toLocaleString() || 0 }}</p>
            </div>
            <div class="rounded-full bg-red-100 p-3">
              <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters and Search -->
      <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
        <form @submit.prevent="applyFilters" class="grid grid-cols-1 gap-4 md:grid-cols-5">
          <div>
            <label class="block text-sm font-medium text-slate-700">Search</label>
            <input
              v-model="form.search"
              type="text"
              placeholder="Title, description, creator..."
              class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700">Status</label>
            <select
              v-model="form.status"
              class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            >
              <option value="">All Statuses</option>
              <option value="active">Active</option>
              <option value="published">Published</option>
              <option value="draft">Draft</option>
              <option value="archived">Archived</option>
              <option value="reported">Reported</option>
              <option value="removed">Removed</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700">Platform</label>
            <select
              v-model="form.platform_id"
              class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            >
              <option value="">All Platforms</option>
              <option v-for="platform in platforms" :key="platform.id" :value="platform.id">
                {{ platform.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700">Date From</label>
            <input
              v-model="form.date_from"
              type="date"
              class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700">Date To</label>
            <input
              v-model="form.date_to"
              type="date"
              class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            />
          </div>
        </form>

        <div class="mt-4 flex gap-2">
          <button
            @click="applyFilters"
            type="button"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition"
          >
            Apply Filters
          </button>
          <button
            @click="clearFilters"
            type="button"
            class="inline-flex items-center px-4 py-2 bg-slate-200 border border-transparent rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition"
          >
            Clear
          </button>
          <button
            v-if="selectedContent.length > 0"
            @click="showBulkActions = true"
            type="button"
            class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ml-auto"
          >
            Bulk Actions ({{ selectedContent.length }})
          </button>
          <button
            @click="exportContent"
            type="button"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export CSV
          </button>
        </div>
      </div>

      <!-- Content Table -->
      <div class="rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left">
                  <input
                    type="checkbox"
                    @change="toggleSelectAll"
                    :checked="selectedContent.length === contents.data.length && contents.data.length > 0"
                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                  />
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                  Content
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                  Creator
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                  Platform
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                  Status
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                  Metrics
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                  Date
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
              <tr v-for="content in contents.data" :key="content.id" class="hover:bg-slate-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <input
                    type="checkbox"
                    :value="content.id"
                    v-model="selectedContent"
                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                  />
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="h-16 w-24 flex-shrink-0 rounded-lg bg-slate-100 overflow-hidden">
                      <img
                        v-if="content.thumbnail_url"
                        :src="content.thumbnail_url"
                        :alt="content.title"
                        class="h-full w-full object-cover"
                      />
                      <div v-else class="h-full w-full flex items-center justify-center text-slate-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                        </svg>
                      </div>
                    </div>
                    <div class="ml-4 max-w-md">
                      <Link
                        :href="route('admin.content.show', content.id)"
                        class="text-sm font-medium text-slate-900 hover:text-blue-600"
                      >
                        {{ content.title || 'Untitled' }}
                      </Link>
                      <p class="text-sm text-slate-500 truncate">{{ content.description || 'No description' }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-xs font-semibold">
                      {{ content.user?.username?.substring(0, 2).toUpperCase() || 'U' }}
                    </div>
                    <div class="ml-3">
                      <p class="text-sm font-medium text-slate-900">{{ content.user?.full_name || content.user?.username || 'Unknown' }}</p>
                      <p class="text-xs text-slate-500">@{{ content.user?.username || 'unknown' }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                    {{ content.platform?.name || 'Unknown' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="{
                      'bg-green-100 text-green-800': content.status === 'published' || content.status === 'active',
                      'bg-yellow-100 text-yellow-800': content.status === 'draft',
                      'bg-slate-100 text-slate-800': content.status === 'archived',
                      'bg-orange-100 text-orange-800': content.status === 'reported',
                      'bg-red-100 text-red-800': content.status === 'removed',
                    }"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                  >
                    {{ content.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                  <div class="flex flex-col space-y-1">
                    <div class="flex items-center">
                      <svg class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                      </svg>
                      <span>{{ content.reports_count || 0 }} reports</span>
                    </div>
                    <div class="flex items-center">
                      <svg class="w-4 h-4 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                      <span>{{ content.feedback_count || 0 }} reviews</span>
                    </div>
                    <div class="flex items-center">
                      <svg class="w-4 h-4 text-blue-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                      </svg>
                      <span>{{ content.engagements_count || 0 }} engagements</span>
                    </div>
                    <div class="flex items-center">
                      <svg class="w-4 h-4 text-pink-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                      </svg>
                      <span>{{ content.likes_count || 0 }} likes</span>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                  <div>{{ formatDate(content.created_at) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end gap-2">
                    <Link
                      :href="route('admin.content.show', content.id)"
                      class="text-blue-600 hover:text-blue-900"
                      title="View Details"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </Link>
                    <button
                      @click="openStatusModal(content)"
                      class="text-purple-600 hover:text-purple-900"
                      title="Change Status"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                      </svg>
                    </button>
                    <button
                      @click="openBanModal(content)"
                      class="text-orange-600 hover:text-orange-900"
                      title="Ban Content"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                      </svg>
                    </button>
                    <button
                      @click="openDeleteModal(content)"
                      class="text-red-600 hover:text-red-900"
                      title="Delete Content"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-slate-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <Link
                v-if="contents.prev_page_url"
                :href="contents.prev_page_url"
                class="relative inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50"
              >
                Previous
              </Link>
              <Link
                v-if="contents.next_page_url"
                :href="contents.next_page_url"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50"
              >
                Next
              </Link>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-slate-700">
                  Showing
                  <span class="font-medium">{{ contents.from || 0 }}</span>
                  to
                  <span class="font-medium">{{ contents.to || 0 }}</span>
                  of
                  <span class="font-medium">{{ contents.total || 0 }}</span>
                  results
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                  <Link
                    v-for="(link, index) in contents.links"
                    :key="index"
                    :href="link.url"
                    v-html="link.label"
                    :class="{
                      'bg-blue-50 border-blue-500 text-blue-600': link.active,
                      'bg-white border-slate-300 text-slate-500 hover:bg-slate-50': !link.active,
                      'rounded-l-md': index === 0,
                      'rounded-r-md': index === contents.links.length - 1,
                    }"
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                  />
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Status Update Modal -->
    <Modal :show="showStatusModal" @close="showStatusModal = false">
      <div class="p-6">
        <h3 class="text-lg font-medium text-slate-900 mb-4">Update Content Status</h3>
        <form @submit.prevent="updateStatus">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700">New Status</label>
              <select
                v-model="statusForm.status"
                required
                class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              >
                <option value="active">Active</option>
                <option value="published">Published</option>
                <option value="draft">Draft</option>
                <option value="archived">Archived</option>
                <option value="reported">Reported</option>
                <option value="removed">Removed</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700">Reason (Optional)</label>
              <textarea
                v-model="statusForm.reason"
                rows="3"
                class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                placeholder="Enter reason for status change..."
              ></textarea>
            </div>
          </div>
          <div class="mt-6 flex justify-end gap-3">
            <button
              type="button"
              @click="showStatusModal = false"
              class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="statusForm.processing"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
            >
              Update Status
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Ban Content Modal -->
    <Modal :show="showBanModal" @close="showBanModal = false">
      <div class="p-6">
        <h3 class="text-lg font-medium text-red-600 mb-4">Ban Content</h3>
        <p class="text-sm text-slate-600 mb-4">This action will remove the content and optionally ban the user.</p>
        <form @submit.prevent="banContent">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700">Reason</label>
              <textarea
                v-model="banForm.reason"
                required
                rows="3"
                class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                placeholder="Enter reason for banning..."
              ></textarea>
            </div>
            <div class="flex items-center">
              <input
                v-model="banForm.ban_user"
                type="checkbox"
                class="rounded border-slate-300 text-red-600 focus:ring-red-500"
              />
              <label class="ml-2 text-sm text-slate-700">Also ban the user</label>
            </div>
          </div>
          <div class="mt-6 flex justify-end gap-3">
            <button
              type="button"
              @click="showBanModal = false"
              class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="banForm.processing"
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
            >
              Ban Content
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Delete Content Modal -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false">
      <div class="p-6">
        <h3 class="text-lg font-medium text-red-600 mb-4">Delete Content</h3>
        <p class="text-sm text-slate-600 mb-4">Are you sure you want to delete this content? This action can be recovered.</p>
        <form @submit.prevent="deleteContent">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700">Reason (Optional)</label>
              <textarea
                v-model="deleteForm.reason"
                rows="3"
                class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                placeholder="Enter reason for deletion..."
              ></textarea>
            </div>
          </div>
          <div class="mt-6 flex justify-end gap-3">
            <button
              type="button"
              @click="showDeleteModal = false"
              class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="deleteForm.processing"
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
            >
              Delete Content
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Bulk Actions Modal -->
    <Modal :show="showBulkActions" @close="showBulkActions = false">
      <div class="p-6">
        <h3 class="text-lg font-medium text-slate-900 mb-4">Bulk Actions ({{ selectedContent.length }} items)</h3>
        <form @submit.prevent="executeBulkAction">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700">Action</label>
              <select
                v-model="bulkForm.status"
                required
                class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              >
                <option value="">Select an action...</option>
                <option value="active">Set to Active</option>
                <option value="published">Set to Published</option>
                <option value="draft">Set to Draft</option>
                <option value="archived">Archive</option>
                <option value="removed">Remove</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700">Reason (Optional)</label>
              <textarea
                v-model="bulkForm.reason"
                rows="3"
                class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                placeholder="Enter reason for bulk action..."
              ></textarea>
            </div>
          </div>
          <div class="mt-6 flex justify-end gap-3">
            <button
              type="button"
              @click="showBulkActions = false"
              class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="bulkForm.processing"
              class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50"
            >
              Execute Bulk Action
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  contents: Object,
  statistics: Object,
  platforms: Array,
  filters: Object,
});

const selectedContent = ref([]);
const showStatusModal = ref(false);
const showBanModal = ref(false);
const showDeleteModal = ref(false);
const showBulkActions = ref(false);
const selectedContentItem = ref(null);

const form = reactive({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  platform_id: props.filters?.platform_id || '',
  date_from: props.filters?.date_from || '',
  date_to: props.filters?.date_to || '',
});

const statusForm = useForm({
  content_id: null,
  status: 'active',
  reason: '',
});

const banForm = useForm({
  content_id: null,
  reason: '',
  ban_user: false,
});

const deleteForm = useForm({
  content_id: null,
  reason: '',
});

const bulkForm = useForm({
  content_ids: [],
  status: '',
  reason: '',
});

const toggleSelectAll = () => {
  if (selectedContent.value.length === props.contents.data.length) {
    selectedContent.value = [];
  } else {
    selectedContent.value = props.contents.data.map(c => c.id);
  }
};

const applyFilters = () => {
  router.get(route('admin.content.index'), form, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  form.search = '';
  form.status = '';
  form.platform_id = '';
  form.date_from = '';
  form.date_to = '';
  applyFilters();
};

const openStatusModal = (content) => {
  selectedContentItem.value = content;
  statusForm.content_id = content.id;
  statusForm.status = content.status;
  statusForm.reason = '';
  showStatusModal.value = true;
};

const updateStatus = () => {
  statusForm.put(route('admin.content.update-status', statusForm.content_id), {
    preserveScroll: true,
    onSuccess: () => {
      showStatusModal.value = false;
      statusForm.reset();
    },
  });
};

const openBanModal = (content) => {
  selectedContentItem.value = content;
  banForm.content_id = content.id;
  banForm.reason = '';
  banForm.ban_user = false;
  showBanModal.value = true;
};

const banContent = () => {
  banForm.post(route('admin.content.ban', banForm.content_id), {
    preserveScroll: true,
    onSuccess: () => {
      showBanModal.value = false;
      banForm.reset();
    },
  });
};

const openDeleteModal = (content) => {
  selectedContentItem.value = content;
  deleteForm.content_id = content.id;
  deleteForm.reason = '';
  showDeleteModal.value = true;
};

const deleteContent = () => {
  deleteForm.delete(route('admin.content.destroy', deleteForm.content_id), {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
      deleteForm.reset();
    },
  });
};

const executeBulkAction = () => {
  bulkForm.content_ids = selectedContent.value;
  bulkForm.post(route('admin.content.bulk-update-status'), {
    preserveScroll: true,
    onSuccess: () => {
      showBulkActions.value = false;
      selectedContent.value = [];
      bulkForm.reset();
    },
  });
};

const exportContent = () => {
  window.location.href = route('admin.content.export', form);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};
</script>
