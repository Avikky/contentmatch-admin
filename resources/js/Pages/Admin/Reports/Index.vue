<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-slate-900">Report Management</h1>
          <p class="mt-1 text-sm text-slate-600">Review and moderate reported content, users, and communities</p>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">Total Reports</p>
              <p class="mt-2 text-3xl font-semibold text-slate-900">{{ statistics.total?.toLocaleString() || 0 }}</p>
            </div>
          </div>
          <p class="mt-3 text-xs text-slate-500">
            <span class="font-medium">{{ statistics.today || 0 }}</span> today
          </p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">Pending</p>
              <p class="mt-2 text-3xl font-semibold text-orange-600">{{ statistics.pending?.toLocaleString() || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">Reviewing</p>
              <p class="mt-2 text-3xl font-semibold text-blue-600">{{ statistics.reviewing?.toLocaleString() || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">Resolved</p>
              <p class="mt-2 text-3xl font-semibold text-green-600">{{ statistics.resolved?.toLocaleString() || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">Dismissed</p>
              <p class="mt-2 text-3xl font-semibold text-slate-600">{{ statistics.dismissed?.toLocaleString() || 0 }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
        <form @submit.prevent="applyFilters" class="grid grid-cols-1 gap-4 md:grid-cols-5">
          <div>
            <label class="block text-sm font-medium text-slate-700">Search</label>
            <input
              v-model="form.search"
              type="text"
              placeholder="Search reports..."
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
            <label class="block text-sm font-medium text-slate-700">Type</label>
            <select
              v-model="form.type"
              class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            >
              <option value="">All Types</option>
              <option value="user">User</option>
              <option value="content">Content</option>
              <option value="community">Community</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700">Reason</label>
            <select
              v-model="form.reason"
              class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            >
              <option value="">All Reasons</option>
              <option value="spam">Spam</option>
              <option value="harassment">Harassment</option>
              <option value="inappropriate">Inappropriate</option>
              <option value="impersonation">Impersonation</option>
              <option value="copyright">Copyright</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div class="flex items-end gap-2">
            <button
              type="submit"
              class="flex-1 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800"
            >
              Apply
            </button>
            <button
              type="button"
              @click="resetFilters"
              class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
              Reset
            </button>
          </div>
        </form>
      </div>

      <!-- Reports Table -->
      <div class="rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Reporter</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Reason</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Date</th>
                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
              <tr v-for="report in reports.data" :key="report.id" class="hover:bg-slate-50">
                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-900">#{{ report.id }}</td>
                
                <td class="whitespace-nowrap px-6 py-4">
                  <div class="text-sm font-medium text-slate-900">
                    {{ report.reporter?.full_name || report.reporter?.username || 'Unknown' }}
                  </div>
                  <div class="text-sm text-slate-500">{{ report.reporter?.email }}</div>
                </td>

                <td class="whitespace-nowrap px-6 py-4">
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                    :class="getTypeClass(report.reportable_type)">
                    {{ getReportableType(report.reportable_type) }}
                  </span>
                </td>

                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-900 capitalize">
                  {{ report.reason.replace('_', ' ') }}
                </td>

                <td class="whitespace-nowrap px-6 py-4">
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                    :class="getStatusClass(report.status)">
                    {{ report.status }}
                  </span>
                </td>

                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">
                  {{ formatDate(report.created_at) }}
                </td>

                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                  <Link :href="`/admin/reports/${report.id}`"
                    class="text-blue-600 hover:text-blue-900">
                    View Details
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="reports.data.length > 0" class="border-t border-slate-200 bg-white px-4 py-3 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="text-sm text-slate-700">
              Showing <span class="font-medium">{{ reports.from }}</span> to <span class="font-medium">{{ reports.to }}</span> of
              <span class="font-medium">{{ reports.total }}</span> reports
            </div>
            <div class="flex gap-2">
              <Link
                v-for="link in reports.links"
                :key="link.label"
                :href="link.url"
                :class="[
                  'px-3 py-2 text-sm font-medium rounded-lg',
                  link.active
                    ? 'bg-slate-900 text-white'
                    : 'border border-slate-300 text-slate-700 hover:bg-slate-50',
                  !link.url && 'opacity-50 cursor-not-allowed'
                ]"
                v-html="link.label"
              />
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="px-6 py-12 text-center">
          <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-slate-900">No reports found</h3>
          <p class="mt-1 text-sm text-slate-500">Try adjusting your filters to see more results.</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  reports: Object,
  statistics: Object,
  filters: Object,
});

const form = ref({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  type: props.filters?.type || '',
  reason: props.filters?.reason || '',
});

const applyFilters = () => {
  router.get('/admin/reports', form.value, {
    preserveState: true,
    preserveScroll: true,
  });
};

const resetFilters = () => {
  form.value = {
    search: '',
    status: '',
    type: '',
    reason: '',
  };
  applyFilters();
};

const getReportableType = (type) => {
  const className = type.split('\\').pop();
  return className;
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

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};
</script>
