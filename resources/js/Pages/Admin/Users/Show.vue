<template>
  <AuthenticatedLayout>
    <div class="space-y-4 sm:space-y-6">
      <!-- Header with Back Button -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
        <Link
          :href="route('admin.users.index')"
          class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50"
        >
          ← Back
        </Link>
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">User Details</h1>
          <p class="mt-1 text-xs sm:text-sm text-slate-600">View and manage user information</p>
        </div>
      </div>

      <!-- User Profile Card -->
      <div class="rounded-xl bg-white p-4 sm:p-6 lg:p-8 shadow-sm border border-slate-200">
        <div class="flex flex-col lg:flex-row items-start gap-4 sm:gap-6">
          <div class="flex items-center gap-3 sm:gap-4 lg:gap-6 w-full lg:w-auto">
            <div class="h-16 w-16 sm:h-20 sm:w-20 flex-shrink-0">
              <div class="flex h-16 w-16 sm:h-20 sm:w-20 items-center justify-center rounded-full bg-slate-900 text-xl sm:text-2xl font-semibold text-white">
                {{ getUserInitials(user) }}
              </div>
            </div>
            <div class="min-w-0 flex-1">
              <h2 class="text-xl sm:text-2xl font-bold text-slate-900 truncate">{{ user.full_name || user.username }}</h2>
              <p class="mt-1 text-sm text-slate-600 truncate">{{ user.email }}</p>
              <div class="mt-2 sm:mt-3 flex flex-wrap items-center gap-2">
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
                <span v-if="user.is_verified" class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-800">
                  Verified
                </span>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex lg:flex-col gap-2 w-full lg:w-auto lg:ml-auto flex-wrap sm:flex-nowrap">
            <button
              v-if="user.status === 'active'"
              @click="showSuspendModal = true"
              class="flex-1 sm:flex-none rounded-lg bg-orange-600 px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-white hover:bg-orange-700 whitespace-nowrap"
            >
              Suspend User
            </button>
            <button
              v-if="user.status === 'active'"
              @click="showBanModal = true"
              class="flex-1 sm:flex-none rounded-lg bg-red-600 px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-white hover:bg-red-700 whitespace-nowrap"
            >
              Ban User
            </button>
            <button
              v-if="user.status !== 'active'"
              @click="showReactivateModal = true"
              class="flex-1 sm:flex-none rounded-lg bg-green-600 px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-white hover:bg-green-700 whitespace-nowrap"
            >
              Reactivate
            </button>
            <button
              @click="showRemoveModal = true"
              class="flex-1 sm:flex-none rounded-lg border border-red-300 bg-white px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-red-700 hover:bg-red-50 whitespace-nowrap"
            >
              Remove User
            </button>
          </div>
        </div>

        <!-- User Stats -->
        <div class="mt-6 sm:mt-8 grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 border-t border-slate-200 pt-4 sm:pt-6">
          <div>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Communities</p>
            <p class="mt-1.5 sm:mt-2 text-xl sm:text-2xl font-semibold text-slate-900">{{ user.communities_count || 0 }}</p>
          </div>
          <div>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Reports</p>
            <p class="mt-1.5 sm:mt-2 text-xl sm:text-2xl font-semibold text-slate-900">{{ user.received_reports_count || 0 }}</p>
          </div>
          <div>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Blocked Users</p>
            <p class="mt-1.5 sm:mt-2 text-xl sm:text-2xl font-semibold text-slate-900">{{ user.blocked_users_count || 0 }}</p>
          </div>
          <div>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Subscriptions</p>
            <p class="mt-1.5 sm:mt-2 text-xl sm:text-2xl font-semibold text-slate-900">{{ user.all_subscriptions_count || 0 }}</p>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="border-b border-slate-200 overflow-x-auto">
        <nav class="-mb-px flex space-x-4 sm:space-x-8 min-w-max px-1">
          <button
            v-for="tab in tabs"
            :key="tab.key"
            @click="activeTab = tab.key"
            :class="{
              'border-blue-500 text-blue-600': activeTab === tab.key,
              'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700': activeTab !== tab.key,
            }"
            class="whitespace-nowrap border-b-2 px-1 py-3 sm:py-4 text-xs sm:text-sm font-medium"
          >
            {{ tab.label }}
          </button>
        </nav>
      </div>

      <!-- Tab Content -->
      <div v-if="activeTab === 'overview'" class="space-y-4 sm:space-y-6">
        <!-- Communities -->
        <div class="rounded-xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200">
          <h3 class="text-base sm:text-lg font-semibold text-slate-900">Communities ({{ user.communities?.length || 0 }})</h3>
          <div v-if="user.communities?.length" class="mt-3 sm:mt-4 space-y-2 sm:space-y-3">
            <div
              v-for="community in user.communities"
              :key="community.id"
              class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-lg border border-slate-200 p-3 sm:p-4"
            >
              <div class="min-w-0">
                <p class="font-medium text-slate-900 text-sm sm:text-base truncate">{{ community.name }}</p>
                <p class="text-xs sm:text-sm text-slate-500 truncate">Role: {{ community.pivot?.role }}</p>
              </div>
              <button
                @click="removeCommunity(community.id)"
                class="rounded-lg border border-red-300 bg-white px-3 py-1.5 text-xs sm:text-sm font-semibold text-red-700 hover:bg-red-50 w-full sm:w-auto"
              >
                Remove
              </button>
            </div>
          </div>
          <p v-else class="mt-3 sm:mt-4 text-xs sm:text-sm text-slate-500">No communities</p>
        </div>

        <!-- Recent Activity -->
        <div class="rounded-xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200">
          <h3 class="text-base sm:text-lg font-semibold text-slate-900">Recent Activity</h3>
          <div v-if="activities?.length" class="mt-3 sm:mt-4 space-y-2 sm:space-y-3">
            <div
              v-for="activity in activities.slice(0, 10)"
              :key="activity.id"
              class="rounded-lg border border-slate-200 p-3 sm:p-4"
            >
              <p class="font-medium text-slate-900 text-sm sm:text-base">{{ activity.type }}</p>
              <p class="text-xs sm:text-sm text-slate-500 mt-1">{{ formatDate(activity.created_at) }}</p>
            </div>
          </div>
          <p v-else class="mt-3 sm:mt-4 text-xs sm:text-sm text-slate-500">No recent activity</p>
        </div>
      </div>

      <div v-if="activeTab === 'reports'" class="rounded-xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200">
        <h3 class="text-base sm:text-lg font-semibold text-slate-900">Reports Against User</h3>
        <div v-if="user.received_reports?.length" class="mt-3 sm:mt-4 space-y-3 sm:space-y-4">
          <div
            v-for="report in user.received_reports"
            :key="report.id"
            class="rounded-lg border border-slate-200 p-3 sm:p-4"
          >
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
              <div class="min-w-0 flex-1">
                <p class="font-medium text-slate-900 text-sm sm:text-base">{{ report.reason }}</p>
                <p class="mt-1 text-xs sm:text-sm text-slate-600">{{ report.description }}</p>
                <p class="mt-2 text-xs text-slate-500">
                  Reported by {{ report.reporter?.full_name || report.reporter?.username }} on {{ formatDate(report.created_at) }}
                </p>
              </div>
              <span
                :class="{
                  'bg-yellow-100 text-yellow-800': report.status === 'pending',
                  'bg-blue-100 text-blue-800': report.status === 'reviewing',
                  'bg-green-100 text-green-800': report.status === 'resolved',
                  'bg-slate-100 text-slate-800': report.status === 'dismissed',
                }"
                class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold whitespace-nowrap flex-shrink-0 self-start"
              >
                {{ report.status }}
              </span>
            </div>
          </div>
        </div>
        <p v-else class="mt-3 sm:mt-4 text-xs sm:text-sm text-slate-500">No reports</p>
      </div>

      <div v-if="activeTab === 'moderation'" class="rounded-xl bg-white p-4 sm:p-5 lg:p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200">
        <h3 class="text-base sm:text-lg font-semibold text-slate-900">Moderation History</h3>
        <div v-if="user.moderation_logs?.length" class="mt-3 sm:mt-4 space-y-3">
          <div
            v-for="log in user.moderation_logs"
            :key="log.id"
            class="rounded-lg border border-slate-200 p-3 sm:p-4"
          >
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
              <div class="min-w-0 flex-1">
                <p class="font-medium text-slate-900 text-sm sm:text-base">{{ log.action }}</p>
                <p class="mt-1 text-xs sm:text-sm text-slate-600">{{ log.reason }}</p>
                <p class="mt-2 text-xs text-slate-500">
                  By {{ log.admin?.display_name || 'System' }} on {{ formatDate(log.created_at) }}
                </p>
              </div>
              <div class="flex-shrink-0">
                <p class="text-xs text-slate-500">Status: {{ log.status_before }} → {{ log.status_after }}</p>
              </div>
            </div>
          </div>
        </div>
        <p v-else class="mt-3 sm:mt-4 text-xs sm:text-sm text-slate-500">No moderation history</p>
      </div>
    </div>

    <!-- Suspend Modal -->
    <div v-if="showSuspendModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex min-h-screen items-center justify-center px-4">
        <div class="fixed inset-0 bg-black/50" @click="showSuspendModal = false"></div>
        <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
          <h3 class="text-lg font-semibold text-slate-900">Suspend User</h3>
          <p class="mt-2 text-sm text-slate-600">Provide a reason for suspending this user:</p>
          <textarea
            v-model="suspendReason"
            rows="4"
            class="mt-4 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            placeholder="Reason for suspension..."
          ></textarea>
          <div class="mt-6 flex gap-3">
            <button
              @click="suspendUser"
              class="flex-1 rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700"
            >
              Confirm Suspend
            </button>
            <button
              @click="showSuspendModal = false"
              class="flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Ban Modal -->
    <div v-if="showBanModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex min-h-screen items-center justify-center px-4">
        <div class="fixed inset-0 bg-black/50" @click="showBanModal = false"></div>
        <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
          <h3 class="text-lg font-semibold text-slate-900">Ban User</h3>
          <p class="mt-2 text-sm text-slate-600">Provide a reason for banning this user:</p>
          <textarea
            v-model="banReason"
            rows="4"
            class="mt-4 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            placeholder="Reason for ban..."
          ></textarea>
          <div class="mt-6 flex gap-3">
            <button
              @click="banUser"
              class="flex-1 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
            >
              Confirm Ban
            </button>
            <button
              @click="showBanModal = false"
              class="flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Reactivate Modal -->
    <div v-if="showReactivateModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex min-h-screen items-center justify-center px-4">
        <div class="fixed inset-0 bg-black/50" @click="showReactivateModal = false"></div>
        <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
          <h3 class="text-lg font-semibold text-slate-900">Reactivate User</h3>
          <p class="mt-2 text-sm text-slate-600">Provide a reason for reactivating this user (optional):</p>
          <textarea
            v-model="reactivateReason"
            rows="4"
            class="mt-4 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            placeholder="Reason for reactivation (optional)..."
          ></textarea>
          <div class="mt-6 flex gap-3">
            <button
              @click="reactivateUser"
              class="flex-1 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700"
            >
              Confirm Reactivate
            </button>
            <button
              @click="showReactivateModal = false"
              class="flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Remove Modal -->
    <div v-if="showRemoveModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex min-h-screen items-center justify-center px-4">
        <div class="fixed inset-0 bg-black/50" @click="showRemoveModal = false"></div>
        <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
          <h3 class="text-lg font-semibold text-slate-900">Remove User</h3>
          <p class="mt-2 text-sm text-red-600">Warning: This will permanently remove the user account.</p>
          <textarea
            v-model="removeReason"
            rows="4"
            class="mt-4 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            placeholder="Reason for removal (optional)..."
          ></textarea>
          <div class="mt-6 flex gap-3">
            <button
              @click="removeUser"
              class="flex-1 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
            >
              Confirm Remove
            </button>
            <button
              @click="showRemoveModal = false"
              class="flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
              Cancel
            </button>
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
  user: Object,
  activities: Array,
  blocked_users: Array,
  blocked_by_users: Array,
});

const activeTab = ref('overview');
const showSuspendModal = ref(false);
const showBanModal = ref(false);
const showReactivateModal = ref(false);
const showRemoveModal = ref(false);

const suspendReason = ref('');
const banReason = ref('');
const reactivateReason = ref('');
const removeReason = ref('');

const tabs = [
  { key: 'overview', label: 'Overview' },
  { key: 'reports', label: 'Reports' },
  { key: 'moderation', label: 'Moderation History' },
];

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
    hour: '2-digit',
    minute: '2-digit',
  });
};

const suspendUser = () => {
  router.post(
    route('admin.users.suspend', props.user.id),
    { reason: suspendReason.value },
    {
      preserveScroll: true,
      onSuccess: () => {
        showSuspendModal.value = false;
        suspendReason.value = '';
      },
    }
  );
};

const banUser = () => {
  router.post(
    route('admin.users.ban', props.user.id),
    { reason: banReason.value },
    {
      preserveScroll: true,
      onSuccess: () => {
        showBanModal.value = false;
        banReason.value = '';
      },
    }
  );
};

const reactivateUser = () => {
  router.post(
    route('admin.users.reactivate', props.user.id),
    { reason: reactivateReason.value },
    {
      preserveScroll: true,
      onSuccess: () => {
        showReactivateModal.value = false;
        reactivateReason.value = '';
      },
    }
  );
};

const removeUser = () => {
  router.delete(route('admin.users.destroy', props.user.id), {
    data: { reason: removeReason.value },
    onSuccess: () => {
      showRemoveModal.value = false;
    },
  });
};

const removeCommunity = (communityId) => {
  if (confirm('Are you sure you want to remove this user from the community?')) {
    router.post(
      route('admin.users.remove-from-community', props.user.id),
      { community_id: communityId },
      {
        preserveScroll: true,
      }
    );
  }
};
</script>
