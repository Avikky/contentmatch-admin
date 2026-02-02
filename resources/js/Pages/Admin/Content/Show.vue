<template>
  <AuthenticatedLayout>
    <Head :title="`Content: ${content.title || 'Untitled'}`" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <Link
            :href="route('admin.content.index')"
            class="text-sm text-blue-600 hover:text-blue-800 flex items-center mb-2"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Content List
          </Link>
          <h1 class="text-3xl font-bold text-slate-900">Content Details</h1>
          <p class="mt-1 text-sm text-slate-600">Complete information and moderation tools</p>
        </div>
        <div class="flex gap-2">
          <button
            @click="openStatusModal"
            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-semibold"
          >
            Change Status
          </button>
          <button
            @click="openBanModal"
            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm font-semibold"
          >
            Ban Content
          </button>
          <button
            @click="openDeleteModal"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-semibold"
          >
            Delete
          </button>
        </div>
      </div>

      <!-- Main Content Section -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Content Details -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Content Preview -->
          <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
            <div class="aspect-video bg-slate-100 rounded-lg overflow-hidden mb-4">
                
                  <iframe v-if="content.platform_content_url" 
                      class="w-full h-full object-cover rounded-xl  w-full h-[400px] sm:h-[460px] sm:rounded-lg"
                      :src="content.platform_content_url"
                      data-singleplay-embed="true"
                      autoplay
                      loading="lazy"
                      :title="content.title"
                      allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"
                      frameborder="0"
                      allowfullscreen
                  >
                  </iframe>
              <!-- <img
                v-if="content.thumbnail_url"
                :src="content.thumbnail_url"
                :alt="content.title"
                class="w-full h-full object-cover"
              /> -->
              <div v-else class="w-full h-full flex items-center justify-center text-slate-400">
                <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                </svg>
              </div>
            </div>

            <h2 class="text-2xl font-bold text-slate-900 mb-2"><a :href="content.url"> {{ content.title || 'Untitled' }} </a></h2>
            <p class="text-slate-600 mb-4">{{ content.description || 'No description provided' }}</p>

            <div class="flex flex-wrap gap-2 mb-4">
              <span
                :class="{
                  'bg-green-100 text-green-800': content.status === 'published' || content.status === 'active',
                  'bg-yellow-100 text-yellow-800': content.status === 'draft',
                  'bg-slate-100 text-slate-800': content.status === 'archived',
                  'bg-orange-100 text-orange-800': content.status === 'reported',
                  'bg-red-100 text-red-800': content.status === 'removed',
                }"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium capitalize"
              >
                {{ content.status }}
              </span>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                {{ content.platform?.name }}
              </span>
              <span v-for="category in content.categories" :key="category.id" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                {{ category.name }}
              </span>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-200">
              <div>
                <p class="text-sm text-slate-500">Content URL</p>
                <a
                  v-if="content.url"
                  :href="content.url"
                  target="_blank"
                  class="text-sm text-blue-600 hover:text-blue-800 truncate block"
                >
                  {{ content.url }}
                </a>
                <p v-else class="text-sm text-slate-400">Not available</p>
              </div>
              <div>
                <p class="text-sm text-slate-500">Thumbnail URL</p>
                <a
                  v-if="content.thumbnail_url"
                  :href="content.thumbnail_url"
                  target="_blank"
                  class="text-sm text-blue-600 hover:text-blue-800 truncate block"
                >
                  View Thumbnail
                </a>
                <p v-else class="text-sm text-slate-400">Not available</p>
              </div>
              <div>
                <p class="text-sm text-slate-500">Created</p>
                <p class="text-sm font-medium text-slate-900">{{ formatDate(content.created_at) }}</p>
              </div>
              <div>
                <p class="text-sm text-slate-500">Last Updated</p>
                <p class="text-sm font-medium text-slate-900">{{ formatDate(content.updated_at) }}</p>
              </div>
            </div>

            <!-- Hashtags -->
            <div v-if="content.hashtags && content.hashtags.length > 0" class="mt-4 pt-4 border-t border-slate-200">
              <p class="text-sm text-slate-500 mb-2">Hashtags</p>
              <div class="flex flex-wrap gap-2">
                <span v-for="hashtag in content.hashtags" :key="hashtag.id" class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                  #{{ hashtag.name }}
                </span>
              </div>
            </div>
          </div>

          <!-- Reports Section -->
          <div v-if="content.reports && content.reports.length > 0" class="rounded-xl bg-white p-6 shadow-sm border border-red-200">
            <h3 class="text-lg font-semibold text-red-600 mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              Reports ({{ content.reports.length }})
            </h3>
            <div class="space-y-3">
              <div
                v-for="report in content.reports"
                :key="report.id"
                class="p-4 rounded-lg bg-red-50 border border-red-100"
              >
                <div class="flex items-start justify-between mb-2">
                  <div class="flex items-center gap-2">
                    <span
                      :class="{
                        'bg-orange-100 text-orange-800': report.status === 'pending',
                        'bg-yellow-100 text-yellow-800': report.status === 'reviewing',
                        'bg-green-100 text-green-800': report.status === 'resolved',
                        'bg-slate-100 text-slate-800': report.status === 'dismissed',
                      }"
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold capitalize"
                    >
                      {{ report.status }}
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 capitalize">
                      {{ report.reason?.replace('_', ' ') }}
                    </span>
                  </div>
                  <span class="text-xs text-slate-500">{{ formatDate(report.created_at) }}</span>
                </div>
                <p class="text-sm text-slate-700 mb-2">{{ report.description }}</p>
                <div class="flex items-center text-xs text-slate-600">
                  <span>Reported by @{{ report.reporter?.username }}</span>
                  <span v-if="report.resolver_id" class="ml-4 flex items-center text-green-600">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Resolved
                  </span>
                </div>
                <div v-if="report.resolution_notes" class="mt-2 pt-2 border-t border-red-200">
                  <p class="text-xs font-medium text-green-700">Resolution: {{ report.resolution_notes }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Feedback Section -->
          <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center justify-between">
              <span class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                Feedback & Ratings ({{ content.feedback?.length || 0 }})
              </span>
              <span class="text-sm font-normal text-slate-600">Avg: {{ stats.avg_rating || 'N/A' }}</span>
            </h3>
            <div v-if="content.feedback && content.feedback.length > 0" class="space-y-3">
              <div
                v-for="feedback in content.feedback"
                :key="feedback.id"
                class="p-4 rounded-lg bg-slate-50"
              >
                <div class="flex items-start justify-between mb-2">
                  <div class="flex items-center">
                    <div class="flex">
                      <svg
                        v-for="star in 5"
                        :key="star"
                        :class="star <= feedback.rating ? 'text-yellow-400' : 'text-slate-300'"
                        class="w-4 h-4"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                    </div>
                    <span class="ml-2 text-sm font-semibold text-slate-900">{{ feedback.rating }}/5</span>
                  </div>
                  <span class="text-xs text-slate-500">{{ formatDate(feedback.created_at) }}</span>
                </div>
                <div v-if="feedback.liked_aspect" class="mb-2">
                  <p class="text-xs font-medium text-green-700 mb-1">👍 Liked:</p>
                  <p class="text-sm text-slate-700">{{ feedback.liked_aspect }}</p>
                </div>
                <div v-if="feedback.improvement_suggestion">
                  <p class="text-xs font-medium text-orange-700 mb-1">💡 Improvement:</p>
                  <p class="text-sm text-slate-700">{{ feedback.improvement_suggestion }}</p>
                </div>
                <div class="mt-2 flex items-center text-xs text-slate-600">
                  <div class="h-5 w-5 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-xs font-semibold">
                    {{ feedback.user?.username?.substring(0, 1).toUpperCase() }}
                  </div>
                  <span class="ml-2">@{{ feedback.user?.username }}</span>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8 text-slate-500">
              <p class="text-sm">No feedback yet</p>
            </div>
          </div>

          <!-- Engagements Section -->
          <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              User Engagements ({{ content.engagements?.length || 0 }})
            </h3>
            <div v-if="content.engagements && content.engagements.length > 0" class="space-y-3">
              <div
                v-for="engagement in content.engagements"
                :key="engagement.id"
                class="p-4 rounded-lg bg-slate-50"
              >
                <div class="flex items-center justify-between mb-2">
                  <div class="flex items-center">
                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-green-500 to-teal-500 flex items-center justify-center text-white text-xs font-semibold">
                      {{ engagement.user?.username?.substring(0, 2).toUpperCase() }}
                    </div>
                    <div class="ml-3">
                      <p class="text-sm font-medium text-slate-900">{{ engagement.user?.full_name || engagement.user?.username }}</p>
                      <p class="text-xs text-slate-500">@{{ engagement.user?.username }}</p>
                    </div>
                  </div>
                  <span class="text-xs text-slate-500">{{ formatDate(engagement.created_at) }}</span>
                </div>
                <div class="flex flex-wrap gap-2 mt-2">
                  <span v-if="engagement.did_subscribe" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                    ✓ Subscribed
                  </span>
                  <span v-if="engagement.did_like" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-pink-100 text-pink-800">
                    ❤️ Liked
                  </span>
                  <span v-if="engagement.did_comment" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                    💬 Commented
                  </span>
                </div>
                <div v-if="engagement.proof_image_url" class="mt-2">
                  <a :href="engagement.proof_image_url" target="_blank" class="text-xs text-blue-600 hover:text-blue-800">
                    View Proof Image
                  </a>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8 text-slate-500">
              <p class="text-sm">No engagements yet</p>
            </div>
          </div>

          <!-- Likes Section -->
          <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2 text-pink-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
              </svg>
              Likes ({{ content.likes?.length || 0 }})
            </h3>
            <div v-if="content.likes && content.likes.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-3">
              <div
                v-for="like in content.likes"
                :key="like.id"
                class="flex items-center p-3 rounded-lg bg-slate-50 hover:bg-slate-100 transition"
              >
                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-pink-500 to-red-500 flex items-center justify-center text-white text-xs font-semibold">
                  {{ like.user?.username?.substring(0, 2).toUpperCase() }}
                </div>
                <div class="ml-3 min-w-0">
                  <p class="text-sm font-medium text-slate-900 truncate">{{ like.user?.username }}</p>
                  <p class="text-xs text-slate-500">{{ formatDate(like.created_at) }}</p>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8 text-slate-500">
              <p class="text-sm">No likes yet</p>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Creator Info -->
          <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Content Creator</h3>
            <div class="flex items-center mb-4">
              <div class="h-16 w-16 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-xl font-bold">
                {{ content.user?.username?.substring(0, 2).toUpperCase() }}
              </div>
              <div class="ml-4">
                <p class="font-semibold text-slate-900">{{ content.user?.full_name || content.user?.username }}</p>
                <p class="text-sm text-slate-600">@{{ content.user?.username }}</p>
                <p class="text-xs text-slate-500">{{ content.user?.email }}</p>
              </div>
            </div>
            <div class="pt-4 border-t border-slate-200 space-y-2">
              <div class="flex justify-between text-sm">
                <span class="text-slate-600">User ID:</span>
                <span class="font-medium text-slate-900">{{ content.user_id }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-slate-600">Joined:</span>
                <span class="font-medium text-slate-900">{{ formatDate(content.user?.created_at) }}</span>
              </div>
            </div>
          </div>

          <!-- Statistics -->
          <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Statistics</h3>
            <div class="space-y-3">
              <div class="flex items-center justify-between p-3 rounded-lg bg-red-50">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  <span class="text-sm text-slate-700">Reports</span>
                </div>
                <span class="text-lg font-semibold text-red-600">{{ stats.reports_count }}</span>
              </div>

              <div class="flex items-center justify-between p-3 rounded-lg bg-yellow-50">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <span class="text-sm text-slate-700">Avg Rating</span>
                </div>
                <span class="text-lg font-semibold text-yellow-600">{{ stats.avg_rating || 'N/A' }}</span>
              </div>

              <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                  <span class="text-sm text-slate-700">Engagements</span>
                </div>
                <span class="text-lg font-semibold text-blue-600">{{ stats.engagements_count }}</span>
              </div>

              <div class="flex items-center justify-between p-3 rounded-lg bg-pink-50">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-pink-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                  </svg>
                  <span class="text-sm text-slate-700">Likes</span>
                </div>
                <span class="text-lg font-semibold text-pink-600">{{ stats.likes_count }}</span>
              </div>

              <div class="flex items-center justify-between p-3 rounded-lg bg-green-50">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                  </svg>
                  <span class="text-sm text-slate-700">Feedback</span>
                </div>
                <span class="text-lg font-semibold text-green-600">{{ stats.feedback_count }}</span>
              </div>
            </div>
          </div>

          <!-- Community Info -->
          <div v-if="content.community" class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Community</h3>
            <div class="flex items-center mb-3">
              <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold">
                {{ content.community.name?.substring(0, 2).toUpperCase() }}
              </div>
              <div class="ml-3">
                <p class="font-semibold text-slate-900">{{ content.community.name }}</p>
                <p class="text-xs text-slate-600">{{ content.community.type }}</p>
              </div>
            </div>
            <p class="text-sm text-slate-600">{{ content.community.description }}</p>
          </div>

          <!-- Platform Info -->
          <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Platform Information</h3>
            <div class="space-y-3">
              <div>
                <p class="text-sm text-slate-600">Platform</p>
                <p class="font-semibold text-slate-900">{{ content.platform?.name }}</p>
              </div>
              <div v-if="content.platform?.description">
                <p class="text-sm text-slate-600">Description</p>
                <p class="text-sm text-slate-700">{{ content.platform.description }}</p>
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
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <svg v-if="statusForm.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ statusForm.processing ? 'Updating...' : 'Update Status' }}</span>
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
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <svg v-if="banForm.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ banForm.processing ? 'Banning...' : 'Ban Content' }}</span>
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
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <svg v-if="deleteForm.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ deleteForm.processing ? 'Deleting...' : 'Delete Content' }}</span>
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  content: Object,
  stats: Object,
});

const showStatusModal = ref(false);
const showBanModal = ref(false);
const showDeleteModal = ref(false);

const statusForm = useForm({
  status: props.content.status,
  reason: '',
});

const banForm = useForm({
  reason: '',
  ban_user: false,
});

const deleteForm = useForm({
  reason: '',
});

const openStatusModal = () => {
  statusForm.status = props.content.status;
  statusForm.reason = '';
  showStatusModal.value = true;
};

const updateStatus = () => {
  statusForm.put(route('admin.content.update-status', props.content.id), {
    preserveScroll: true,
    onSuccess: () => {
      showStatusModal.value = false;
    },
  });
};

const openBanModal = () => {
  banForm.reason = '';
  banForm.ban_user = false;
  showBanModal.value = true;
};

const banContent = () => {
  banForm.post(route('admin.content.ban', props.content.id), {
    preserveScroll: true,
    onSuccess: () => {
      showBanModal.value = false;
    },
  });
};

const openDeleteModal = () => {
  deleteForm.reason = '';
  showDeleteModal.value = true;
};

const deleteContent = () => {
  deleteForm.delete(route('admin.content.destroy', props.content.id), {
    onSuccess: () => {
      router.visit(route('admin.content.index'));
    },
  });
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};
</script>
