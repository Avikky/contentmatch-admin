<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
  admins: Object,
  filters: Object,
});

const searchQuery = ref(props.filters?.search || '');
const roleFilter = ref(props.filters?.role || '');
const statusFilter = ref(props.filters?.status || '');

const applyFilters = () => {
  router.get(route('admin.admins.index'), {
    search: searchQuery.value,
    role: roleFilter.value,
    status: statusFilter.value,
  }, {
    preserveState: true,
    replace: true,
  });
};

const toggleStatus = (adminId) => {
  if (confirm('Are you sure you want to toggle this admin\'s status?')) {
    router.post(route('admin.admins.toggle-status', adminId), {}, {
      preserveState: true,
    });
  }
};

const deleteAdmin = (adminId) => {
  if (confirm('Are you sure you want to delete this admin? This action cannot be undone.')) {
    router.delete(route('admin.admins.destroy', adminId), {
      preserveState: true,
    });
  }
};

const getRoleBadgeClass = (role) => {
  const classes = {
    superadmin: 'bg-purple-100 text-purple-800 ring-purple-600/20',
    admin: 'bg-blue-100 text-blue-800 ring-blue-600/20',
    moderator: 'bg-green-100 text-green-800 ring-green-600/20',
  };
  return classes[role] || 'bg-gray-100 text-gray-800 ring-gray-600/20';
};

const getStatusBadgeClass = (isActive) => {
  return isActive
    ? 'bg-green-100 text-green-800 ring-green-600/20'
    : 'bg-red-100 text-red-800 ring-red-600/20';
};
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Admin Management" />

    <section class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-semibold text-slate-900">Admin Management</h1>
          <p class="mt-1 text-sm text-slate-600">Manage admin accounts and permissions</p>
        </div>
        <Link
          :href="route('admin.admins.create')"
          class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#14092A] to-[#2B0F4A] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
          </svg>
          Create Admin
        </Link>
      </div>

      <!-- Filters -->
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-4 md:grid-cols-4">
          <div class="md:col-span-2">
            <label for="search" class="block text-xs font-semibold uppercase tracking-wide text-slate-600 mb-2">
              Search
            </label>
            <input
              id="search"
              v-model="searchQuery"
              type="text"
              placeholder="Search by name or email..."
              @input="applyFilters"
              class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
            />
          </div>
          
          <div>
            <label for="role" class="block text-xs font-semibold uppercase tracking-wide text-slate-600 mb-2">
              Role
            </label>
            <select
              id="role"
              v-model="roleFilter"
              @change="applyFilters"
              class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
            >
              <option value="">All Roles</option>
              <option value="superadmin">Super Admin</option>
              <option value="admin">Admin</option>
              <option value="moderator">Moderator</option>
            </select>
          </div>
          
          <div>
            <label for="status" class="block text-xs font-semibold uppercase tracking-wide text-slate-600 mb-2">
              Status
            </label>
            <select
              id="status"
              v-model="statusFilter"
              @change="applyFilters"
              class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
            >
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Admin List -->
      <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Admin</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Role</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Status</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Last Login</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Created</th>
                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-600">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <tr v-for="admin in admins.data" :key="admin.id" class="hover:bg-slate-50 transition">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img :src="admin.profile_photo_url" :alt="admin.full_name" class="w-10 h-10 rounded-full" />
                    <div>
                      <p class="text-sm font-semibold text-slate-900">{{ admin.full_name }}</p>
                      <p class="text-xs text-slate-500">{{ admin.email }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span :class="getRoleBadgeClass(admin.role)" class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset">
                    {{ admin.role.charAt(0).toUpperCase() + admin.role.slice(1) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span :class="getStatusBadgeClass(admin.is_active)" class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset">
                    {{ admin.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <p class="text-sm text-slate-600">{{ admin.last_login_at || 'Never' }}</p>
                </td>
                <td class="px-6 py-4">
                  <p class="text-sm text-slate-600">{{ admin.created_at }}</p>
                  <p v-if="admin.creator" class="text-xs text-slate-400">by {{ admin.creator }}</p>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-end gap-2">
                    <Link
                      :href="route('admin.admins.edit', admin.id)"
                      class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200 transition"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                      </svg>
                      Edit
                    </Link>
                    
                    <button
                      @click="toggleStatus(admin.id)"
                      class="inline-flex items-center gap-1 rounded-lg bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-200 transition"
                    >
                      {{ admin.is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                    
                    <button
                      @click="deleteAdmin(admin.id)"
                      class="inline-flex items-center gap-1 rounded-lg bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-200 transition"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                      </svg>
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
              
              <tr v-if="admins.data.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                  No admins found matching your filters.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="admins.links && admins.links.length > 3" class="border-t border-slate-200 px-6 py-4">
          <div class="flex items-center justify-between">
            <p class="text-sm text-slate-600">
              Showing {{ admins.from }} to {{ admins.to }} of {{ admins.total }} results
            </p>
            <div class="flex gap-2">
              <Link
                v-for="link in admins.links"
                :key="link.label"
                :href="link.url"
                :class="[
                  'px-3 py-1.5 text-sm rounded-lg transition',
                  link.active
                    ? 'bg-slate-900 text-white'
                    : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                ]"
                v-html="link.label"
              />
            </div>
          </div>
        </div>
      </div>
    </section>
  </AuthenticatedLayout>
</template>
