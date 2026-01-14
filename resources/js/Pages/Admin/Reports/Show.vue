<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <!-- Header with Back Button -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <Link href="/admin/reports" class="text-slate-600 hover:text-slate-900">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </Link>
          <div>
            <h1 class="text-3xl font-bold text-slate-900">Report #{{ report.id }}</h1>
            <p class="mt-1 text-sm text-slate-600">Review report details and take action</p>
          </div>
        </div>

        <div class="flex gap-2">
          <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
            :class="getStatusClass(report.status)">
            {{ report.status }}
          </span>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Report Details -->
          <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Report Details</h2>
            
            <dl class="grid grid-cols-2 gap-4">
              <div>
                <dt class="text-sm font-medium text-slate-500">Report ID</dt>
                <dd class="mt-1 text-sm text-slate-900">#{{ report.id }}</dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-slate-500">Reported Type</dt>
                <dd class="mt-1">
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                    :class="getTypeClass(report.reportable_type)">
                    {{ getReportableType(report.reportable_type) }}
                  </span>
                </dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-slate-500">Reason</dt>
                <dd class="mt-1 text-sm text-slate-900 capitalize">
                  {{ report.reason.replace('_', ' ') }}
                </dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-slate-500">Reported Date</dt>
                <dd class="mt-1 text-sm text-slate-900">
                  {{ formatDateTime(report.created_at) }}
                </dd>
              </div>

              <div class="col-span-2" v-if="report.description">
                <dt class="text-sm font-medium text-slate-500">Description</dt>
                <dd class="mt-1 text-sm text-slate-900 bg-slate-50 rounded-lg p-3">
                  {{ report.description }}
                </dd>
              </div>
            </dl>
          </div>

          <!-- Reporter Information -->
          <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Reporter Information</h2>
            
            <div v-if="report.reporter" class="flex items-center gap-4">
              <div class="h-12 w-12 flex-shrink-0 rounded-full bg-slate-900 text-white flex items-center justify-center text-sm font-semibold">
                {{ getInitials(report.reporter.full_name || report.reporter.username) }}
              </div>
              <div class="flex-1">
                <div class="text-sm font-medium text-slate-900">
                  {{ report.reporter.full_name || report.reporter.username }}
                </div>
                <div class="text-sm text-slate-500">{{ report.reporter.email }}</div>
              </div>
              <Link :href="`/admin/users/${report.reporter.id}`"
                class="text-sm text-blue-600 hover:text-blue-900">
                View Profile
              </Link>
            </div>
          </div>

          <!-- Reported Item Context -->
          <div v-if="context && Object.keys(context).length > 0" class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Reported Item Context</h2>
            
            <dl class="space-y-3">
              <div v-if="context.type === 'user'">
                <dt class="text-sm font-medium text-slate-500">User Status</dt>
                <dd class="mt-1">
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                    :class="getUserStatusClass(context.user_status)">
                    {{ context.user_status }}
                  </span>
                </dd>
                <dt class="text-sm font-medium text-slate-500 mt-3">Total Reports Against User</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ context.total_reports }}</dd>
              </div>

              <div v-if="context.type === 'content'">
                <dt class="text-sm font-medium text-slate-500">Content Status</dt>
                <dd class="mt-1">
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium">
                    {{ context.content_status }}
                  </span>
                </dd>
                <dt class="text-sm font-medium text-slate-500 mt-3">Author</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ context.author?.username }}</dd>
              </div>

              <div v-if="context.type === 'community'">
                <dt class="text-sm font-medium text-slate-500">Community Status</dt>
                <dd class="mt-1">
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium">
                    {{ context.community_status }}
                  </span>
                </dd>
                <dt class="text-sm font-medium text-slate-500 mt-3">Members</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ context.members_count }}</dd>
              </div>
            </dl>
          </div>

          <!-- Resolution Details -->
          <div v-if="report.resolved_at" class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Resolution Details</h2>
            
            <dl class="space-y-3">
              <div>
                <dt class="text-sm font-medium text-slate-500">Resolved By</dt>
                <dd class="mt-1 text-sm text-slate-900">
                  {{ report.resolver?.full_name || 'System' }}
                </dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-slate-500">Resolved At</dt>
                <dd class="mt-1 text-sm text-slate-900">
                  {{ formatDateTime(report.resolved_at) }}
                </dd>
              </div>

              <div v-if="report.resolution_notes">
                <dt class="text-sm font-medium text-slate-500">Resolution Notes</dt>
                <dd class="mt-1 text-sm text-slate-900 bg-slate-50 rounded-lg p-3">
                  {{ report.resolution_notes }}
                </dd>
              </div>
            </dl>
          </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="space-y-6">
          <!-- Quick Actions -->
          <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Actions</h2>
            
            <div class="space-y-3">
              <button
                v-if="report.status === 'pending'"
                @click="markAsReviewing"
                :disabled="processing"
                class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
              >
                <svg v-if="processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ processing ? 'Processing...' : 'Start Reviewing' }}</span>
              </button>

              <button
                v-if="['pending', 'reviewing'].includes(report.status)"
                @click="showResolveModal = true"
                :disabled="processing"
                class="w-full rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Resolve Report
              </button>

              <button
                v-if="['pending', 'reviewing'].includes(report.status)"
                @click="showDismissModal = true"
                :disabled="processing"
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Dismiss Report
              </button>
            </div>
          </div>

          <!-- View Reported Item -->
          <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">View Reported Item</h2>
            
            <Link v-if="context?.type === 'user' && report.reportable"
              :href="`/admin/users/${report.reportable.id}`"
              class="block w-full rounded-lg bg-slate-900 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-slate-800"
            >
              View User Profile
            </Link>

            <Link v-if="context?.type === 'content' && report.reportable"
              :href="`/admin/content/${report.reportable.id}`"
              class="block w-full rounded-lg bg-slate-900 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-slate-800"
            >
              View Content
            </Link>

            <Link v-if="context?.type === 'community' && report.reportable"
              :href="`/admin/communities/${report.reportable.id}`"
              class="block w-full rounded-lg bg-slate-900 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-slate-800"
            >
              View Community
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Resolve Modal -->
    <div v-if="showResolveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-semibold text-slate-900 mb-4">Resolve Report</h3>
        
        <form @submit.prevent="resolveReport">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700">Action on Reported Item</label>
              <select
                v-model="resolveForm.action_on_reportable"
                class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              >
                <option value="none">No Action</option>
                <option value="ban">Ban</option>
                <option value="suspend">Suspend</option>
                <option value="delete">Delete</option>
                <option value="flag">Flag</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700">Resolution Notes</label>
              <textarea
                v-model="resolveForm.resolution_notes"
                rows="4"
                class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                placeholder="Enter resolution details..."
              ></textarea>
            </div>
          </div>

          <div class="mt-6 flex gap-3">
            <button
              type="submit"
              :disabled="processing"
              class="flex-1 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <svg v-if="processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ processing ? 'Resolving...' : 'Resolve' }}</span>
            </button>
            <button
              type="button"
              @click="showResolveModal = false"
              class="flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Dismiss Modal -->
    <div v-if="showDismissModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-semibold text-slate-900 mb-4">Dismiss Report</h3>
        
        <form @submit.prevent="dismissReport">
          <div>
            <label class="block text-sm font-medium text-slate-700">Dismissal Notes</label>
            <textarea
              v-model="dismissForm.resolution_notes"
              rows="4"
              class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="Why are you dismissing this report?"
            ></textarea>
          </div>

          <div class="mt-6 flex gap-3">
            <button
              type="submit"
              :disabled="processing"
              class="flex-1 rounded-lg bg-slate-600 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <svg v-if="processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ processing ? 'Dismissing...' : 'Dismiss' }}</span>
            </button>
            <button
              type="button"
              @click="showDismissModal = false"
              class="flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  report: Object,
  context: Object,
});

const processing = ref(false);
const showResolveModal = ref(false);
const showDismissModal = ref(false);

const resolveForm = ref({
  action_on_reportable: 'none',
  resolution_notes: '',
});

const dismissForm = ref({
  resolution_notes: '',
});

const markAsReviewing = () => {
  processing.value = true;
  router.post(`/admin/reports/${props.report.id}/review`, {}, {
    preserveScroll: true,
    onFinish: () => processing.value = false,
  });
};

const resolveReport = () => {
  processing.value = true;
  router.post(`/admin/reports/${props.report.id}/resolve`, resolveForm.value, {
    preserveScroll: true,
    onSuccess: () => {
      showResolveModal.value = false;
      resolveForm.value = { action_on_reportable: 'none', resolution_notes: '' };
    },
    onFinish: () => processing.value = false,
  });
};

const dismissReport = () => {
  processing.value = true;
  router.post(`/admin/reports/${props.report.id}/dismiss`, dismissForm.value, {
    preserveScroll: true,
    onSuccess: () => {
      showDismissModal.value = false;
      dismissForm.value = { resolution_notes: '' };
    },
    onFinish: () => processing.value = false,
  });
};

const getReportableType = (type) => {
  return type.split('\\').pop();
};

const getTypeClass = (type) => {
  const className = type.split('\\').pop();
  const classes = {
    'User': 'bg-purple-100 text-purple-800',
    'Content': 'bg-blue-100 text-blue-800',
    'Community': 'bg-green-100 text-green-800',
  };
  return classes[className] || 'bg-slate-100 text-slate-800';
};

const getStatusClass = (status) => {
  const classes = {
    'pending': 'bg-orange-100 text-orange-800',
    'reviewing': 'bg-blue-100 text-blue-800',
    'resolved': 'bg-green-100 text-green-800',
    'dismissed': 'bg-slate-100 text-slate-800',
  };
  return classes[status] || 'bg-slate-100 text-slate-800';
};

const getUserStatusClass = (status) => {
  const classes = {
    'active': 'bg-green-100 text-green-800',
    'suspended': 'bg-orange-100 text-orange-800',
    'banned': 'bg-red-100 text-red-800',
  };
  return classes[status] || 'bg-slate-100 text-slate-800';
};

const getInitials = (name) => {
  if (!name) return '?';
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

const formatDateTime = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>
