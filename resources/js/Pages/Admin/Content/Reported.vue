<template>
  <AuthenticatedLayout>
    <Head title="Reported Content" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-slate-900">Reported Content</h1>
          <p class="mt-1 text-sm text-slate-600">Review and resolve user reports</p>
        </div>
        <Link
          :href="route('admin.content.index')"
          class="inline-flex items-center px-4 py-2 bg-slate-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition"
        >
          Back to Content
        </Link>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">Total Reports</p>
              <p class="mt-2 text-3xl font-semibold text-slate-900">{{ statistics.total?.toLocaleString() || 0 }}</p>
            </div>
            <div class="rounded-full bg-blue-100 p-3">
              <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">Pending</p>
              <p class="mt-2 text-3xl font-semibold text-orange-600">{{ statistics.pending?.toLocaleString() || 0 }}</p>
            </div>
            <div class="rounded-full bg-orange-100 p-3">
              <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">Reviewing</p>
              <p class="mt-2 text-3xl font-semibold text-yellow-600">{{ statistics.reviewing?.toLocaleString() || 0 }}</p>
            </div>
            <div class="rounded-full bg-yellow-100 p-3">
              <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">Resolved</p>
              <p class="mt-2 text-3xl font-semibold text-green-600">{{ statistics.resolved?.toLocaleString() || 0 }}</p>
            </div>
            <div class="rounded-full bg-green-100 p-3">
              <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
        <form @submit.prevent="applyFilters" class="grid grid-cols-1 gap-4 md:grid-cols-4">
          <div>
            <label class="block text-sm font-medium text-slate-700">Search</label>
            <input
              v-model="form.search"
              type="text"
              placeholder="Content title, reporter..."
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
              <option value="pending">Pending</option>
              <option value="reviewing">Reviewing</option>
              <option value="resolved">Resolved</option>
              <option value="dismissed">Dismissed</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700">Reason</label>
            <select
              v-model="form.reason"
              class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            >
              <option value="">All Reasons</option>
              <option value="inappropriate">Inappropriate Content</option>
              <option value="spam">Spam</option>
              <option value="harassment">Harassment</option>
              <option value="violence">Violence</option>
              <option value="hate_speech">Hate Speech</option>
              <option value="misinformation">Misinformation</option>
              <option value="copyright">Copyright Violation</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div class="flex items-end">
            <button
              type="submit"
              class="w-full px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition"
            >
              Apply Filters
            </button>
          </div>
        </form>
      </div>

      <!-- Reports List -->
      <div class="space-y-4">
        <div
          v-for="report in reports.data"
          :key="report.id"
          class="rounded-xl bg-white p-6 shadow-sm border border-slate-200 hover:shadow-md transition"
        >
          <div class="flex items-start justify-between">
            <!-- Report Info -->
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-4">
                <span
                  :class="{
                    'bg-orange-100 text-orange-800': report.status === 'pending',
                    'bg-yellow-100 text-yellow-800': report.status === 'reviewing',
                    'bg-green-100 text-green-800': report.status === 'resolved',
                    'bg-slate-100 text-slate-800': report.status === 'dismissed',
                  }"
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold capitalize"
                >
                  {{ report.status }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 capitalize">
                  {{ report.reason?.replace('_', ' ') }}
                </span>
                <span class="text-xs text-slate-500">
                  Reported {{ formatDate(report.created_at) }}
                </span>
              </div>

              <!-- Content Info -->
              <div class="flex gap-4 mb-4">
                <div class="h-24 w-32 flex-shrink-0 rounded-lg bg-slate-100 overflow-hidden">
                  <img
                    v-if="report.reportable?.thumbnail_url"
                    :src="report.reportable.thumbnail_url"
                    :alt="report.reportable.title"
                    class="h-full w-full object-cover"
                  />
                  <div v-else class="h-full w-full flex items-center justify-center text-slate-400">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                    </svg>
                  </div>
                </div>
                <div class="flex-1">
                  <Link
                    :href="route('admin.content.show', report.reportable_id)"
                    class="text-lg font-semibold text-slate-900 hover:text-blue-600"
                  >
                    {{ report.reportable?.title || 'Untitled Content' }}
                  </Link>
                  <p class="text-sm text-slate-600 mt-1">{{ report.reportable?.description || 'No description' }}</p>
                  <div class="flex items-center gap-4 mt-2 text-xs text-slate-500">
                    <span>Platform: {{ report.reportable?.platform?.name }}</span>
                    <span>•</span>
                    <span>Creator: @{{ report.reportable?.user?.username }}</span>
                  </div>
                </div>
              </div>

              <!-- Report Details -->
              <div class="bg-slate-50 rounded-lg p-4 mb-4">
                <p class="text-sm font-medium text-slate-700 mb-2">Report Description:</p>
                <p class="text-sm text-slate-600">{{ report.description || 'No description provided' }}</p>
              </div>

              <!-- Reporter Info -->
              <div class="flex items-center gap-3 text-sm text-slate-600">
                <div class="flex items-center">
                  <div class="h-6 w-6 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-xs font-semibold">
                    {{ report.reporter?.username?.substring(0, 2).toUpperCase() || 'U' }}
                  </div>
                  <span class="ml-2">Reported by <span class="font-medium">@{{ report.reporter?.username }}</span></span>
                </div>
                <span v-if="report.resolver_id" class="ml-4 flex items-center">
                  <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Resolved by Admin
                </span>
              </div>

              <!-- Resolution Notes (if resolved) -->
              <div v-if="report.resolution_notes" class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm font-medium text-green-800 mb-1">Resolution Notes:</p>
                <p class="text-sm text-green-700">{{ report.resolution_notes }}</p>
              </div>
            </div>

            <!-- Actions -->
            <div class="ml-6 flex flex-col gap-2">
              <button
                v-if="report.status === 'pending' || report.status === 'reviewing'"
                @click="openResolveModal(report, 'dismiss')"
                class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-xs font-semibold"
              >
                Dismiss
              </button>
              <button
                v-if="report.status === 'pending' || report.status === 'reviewing'"
                @click="openResolveModal(report, 'warn_user')"
                class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 text-xs font-semibold"
              >
                Warn User
              </button>
              <button
                v-if="report.status === 'pending' || report.status === 'reviewing'"
                @click="openResolveModal(report, 'remove_content')"
                class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-xs font-semibold"
              >
                Remove Content
              </button>
              <button
                v-if="report.status === 'pending' || report.status === 'reviewing'"
                @click="openResolveModal(report, 'ban_content')"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-xs font-semibold"
              >
                Ban Content
              </button>
              <Link
                :href="route('admin.content.show', report.reportable_id)"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs font-semibold text-center"
              >
                View Details
              </Link>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="reports.data.length === 0" class="rounded-xl bg-white p-12 shadow-sm border border-slate-200 text-center">
          <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-slate-900">No reports found</h3>
          <p class="mt-1 text-sm text-slate-500">Try adjusting your filters or check back later.</p>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="reports.data.length > 0" class="rounded-xl bg-white p-4 shadow-sm border border-slate-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-700">
              Showing
              <span class="font-medium">{{ reports.from || 0 }}</span>
              to
              <span class="font-medium">{{ reports.to || 0 }}</span>
              of
              <span class="font-medium">{{ reports.total || 0 }}</span>
              results
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <Link
                v-for="(link, index) in reports.links"
                :key="index"
                :href="link.url"
                v-html="link.label"
                :class="{
                  'bg-blue-50 border-blue-500 text-blue-600': link.active,
                  'bg-white border-slate-300 text-slate-500 hover:bg-slate-50': !link.active,
                  'rounded-l-md': index === 0,
                  'rounded-r-md': index === reports.links.length - 1,
                }"
                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
              />
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Resolve Report Modal -->
    <Modal :show="showResolveModal" @close="showResolveModal = false">
      <div class="p-6">
        <h3 class="text-lg font-medium text-slate-900 mb-4">
          Resolve Report - {{ actionLabel }}
        </h3>
        <p class="text-sm text-slate-600 mb-4">{{ actionDescription }}</p>
        <form @submit.prevent="resolveReport">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700">Resolution Notes</label>
              <textarea
                v-model="resolveForm.resolution_notes"
                required
                rows="4"
                class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                placeholder="Explain your decision..."
              ></textarea>
            </div>
            <div v-if="resolveForm.action === 'ban_content'" class="flex items-center">
              <input
                v-model="resolveForm.ban_user"
                type="checkbox"
                class="rounded border-slate-300 text-red-600 focus:ring-red-500"
              />
              <label class="ml-2 text-sm text-slate-700">Also ban the content creator</label>
            </div>
          </div>
          <div class="mt-6 flex justify-end gap-3">
            <button
              type="button"
              @click="showResolveModal = false"
              class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="resolveForm.processing"
              :class="{
                'bg-slate-600 hover:bg-slate-700': resolveForm.action === 'dismiss',
                'bg-yellow-600 hover:bg-yellow-700': resolveForm.action === 'warn_user',
                'bg-orange-600 hover:bg-orange-700': resolveForm.action === 'remove_content',
                'bg-red-600 hover:bg-red-700': resolveForm.action === 'ban_content',
              }"
              class="px-4 py-2 text-white rounded-lg disabled:opacity-50"
            >
              Confirm Action
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  reports: Object,
  statistics: Object,
  filters: Object,
});

const showResolveModal = ref(false);
const selectedReport = ref(null);

const form = reactive({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  reason: props.filters?.reason || '',
});

const resolveForm = useForm({
  report_id: null,
  action: '',
  resolution_notes: '',
  ban_user: false,
});

const actionLabel = computed(() => {
  const labels = {
    dismiss: 'Dismiss Report',
    warn_user: 'Warn User',
    remove_content: 'Remove Content',
    ban_content: 'Ban Content',
  };
  return labels[resolveForm.action] || 'Resolve Report';
});

const actionDescription = computed(() => {
  const descriptions = {
    dismiss: 'This report will be marked as dismissed. No action will be taken against the content or creator.',
    warn_user: 'The content creator will receive a warning. The content will remain active.',
    remove_content: 'The content will be removed from the platform but the creator will not be banned.',
    ban_content: 'The content will be banned and all associated reports will be resolved. Optionally ban the creator.',
  };
  return descriptions[resolveForm.action] || '';
});

const applyFilters = () => {
  router.get(route('admin.content.reported'), form, {
    preserveState: true,
    preserveScroll: true,
  });
};

const openResolveModal = (report, action) => {
  selectedReport.value = report;
  resolveForm.report_id = report.id;
  resolveForm.action = action;
  resolveForm.resolution_notes = '';
  resolveForm.ban_user = false;
  showResolveModal.value = true;
};

const resolveReport = () => {
  resolveForm.post(route('admin.reports.resolve', resolveForm.report_id), {
    preserveScroll: true,
    onSuccess: () => {
      showResolveModal.value = false;
      resolveForm.reset();
    },
  });
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
</script>
