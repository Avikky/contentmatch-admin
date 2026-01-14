<script setup>
import { ref, reactive, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  admins: Object,
  filters: Object,
});

// Search and filter form
const searchForm = useForm({
  search: props.filters.search || '',
  role: props.filters.role || '',
  status: props.filters.status || '',
});

// Create new admin form
const createForm = useForm({
  full_name: '',
  email: '',
  role: 'admin',
});

// Edit admin form
const editForm = useForm({
  full_name: '',
  email: '',
  role: '',
  is_active: true,
});

// Modals
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedAdmin = ref(null);

// Generate random password
const generatePassword = () => {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
  let password = '';
  for (let i = 0; i < 12; i++) {
    password += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return password;
};

// Apply search/filters
const applyFilters = () => {
  searchForm.get(route('admin.admins.index'), {
    preserveState: true,
    preserveScroll: true,
  });
};

// Reset filters
const resetFilters = () => {
  searchForm.reset();
  searchForm.get(route('admin.admins.index'));
};

// Open create modal
const openCreateModal = () => {
  createForm.reset();
  showCreateModal.value = true;
};

// Submit create form
const submitCreate = () => {
  createForm.post(route('admin.admins.store'), {
    preserveScroll: true,
    onSuccess: () => {
      showCreateModal.value = false;
      createForm.reset();
    },
  });
};

// Open edit modal
const openEditModal = (admin) => {
  selectedAdmin.value = admin;
  editForm.full_name = admin.full_name;
  editForm.email = admin.email;
  editForm.role = admin.role;
  editForm.is_active = admin.is_active;
  showEditModal.value = true;
};

// Submit edit form
const submitEdit = () => {
  editForm.put(route('admin.admins.update', selectedAdmin.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false;
      editForm.reset();
      selectedAdmin.value = null;
    },
  });
};

// Open delete modal
const openDeleteModal = (admin) => {
  selectedAdmin.value = admin;
  showDeleteModal.value = true;
};

// Delete admin
const deleteAdmin = () => {
  router.delete(route('admin.admins.destroy', selectedAdmin.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
      selectedAdmin.value = null;
    },
  });
};

// Toggle admin status
const toggleStatus = (admin) => {
  router.post(route('admin.admins.toggle-status', admin.id), {}, {
    preserveScroll: true,
  });
};

// Get initials for avatar
const getInitials = (name) => {
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .substring(0, 2)
    .toUpperCase();
};

// Get role badge color
const getRoleBadgeColor = (role) => {
  const colors = {
    superadmin: 'bg-purple-100 text-purple-800',
    admin: 'bg-blue-100 text-blue-800',
    moderator: 'bg-green-100 text-green-800',
  };
  return colors[role] || 'bg-gray-100 text-gray-800';
};

// Format date
const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};
</script>

<template>
  <Head title="Admin Management" />

  <AuthenticatedLayout>
    <div class="space-y-4 sm:space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Admin Management</h1>
          <p class="mt-1 text-xs sm:text-sm text-slate-600">Manage administrator accounts and permissions</p>
        </div>
        <button
          @click="openCreateModal"
          class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 transition"
        >
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Create Admin
        </button>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 gap-3 sm:gap-4 lg:gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
              <p class="text-xs sm:text-sm font-medium text-slate-600">Total Admins</p>
              <p class="mt-1.5 sm:mt-2 text-2xl sm:text-3xl font-semibold text-slate-900">{{ admins.total || 0 }}</p>
            </div>
            <div class="rounded-full bg-purple-100 p-2.5 sm:p-3 flex-shrink-0">
              <svg class="h-5 w-5 sm:h-6 sm:w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
              <p class="text-xs sm:text-sm font-medium text-slate-600">Active</p>
              <p class="mt-1.5 sm:mt-2 text-2xl sm:text-3xl font-semibold text-green-600">
                {{ admins.data.filter(a => a.is_active).length }}
              </p>
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
              <p class="text-xs sm:text-sm font-medium text-slate-600">Superadmins</p>
              <p class="mt-1.5 sm:mt-2 text-2xl sm:text-3xl font-semibold text-purple-600">
                {{ admins.data.filter(a => a.role === 'superadmin').length }}
              </p>
            </div>
            <div class="rounded-full bg-purple-100 p-2.5 sm:p-3 flex-shrink-0">
              <svg class="h-5 w-5 sm:h-6 sm:w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
              <p class="text-xs sm:text-sm font-medium text-slate-600">Inactive</p>
              <p class="mt-1.5 sm:mt-2 text-2xl sm:text-3xl font-semibold text-red-600">
                {{ admins.data.filter(a => !a.is_active).length }}
              </p>
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
              v-model="searchForm.search"
              type="text"
              placeholder="Name or email..."
              class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
            />
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Role</label>
            <select
              v-model="searchForm.role"
              class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
            >
              <option value="">All Roles</option>
              <option value="superadmin">Superadmin</option>
              <option value="admin">Admin</option>
              <option value="moderator">Moderator</option>
            </select>
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Status</label>
            <select
              v-model="searchForm.status"
              class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
            >
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div class="flex items-end gap-2">
            <button
              type="submit"
              class="flex-1 rounded-lg bg-slate-900 px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-white hover:bg-slate-800"
            >
              Apply
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

      <!-- Admins Table (Desktop) -->
      <div class="hidden lg:block rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Admin</th>
                <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Role</th>
                <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Status</th>
                <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Created By</th>
                <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Last Login</th>
                <th class="px-4 xl:px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
              <tr v-for="admin in admins.data" :key="admin.id" class="hover:bg-slate-50">
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4">
                  <div class="flex items-center gap-3">
                    <div class="h-10 w-10 flex-shrink-0">
                      <div v-if="admin.profile_photo_url" class="h-10 w-10 rounded-full overflow-hidden">
                        <img :src="admin.profile_photo_url" :alt="admin.full_name" class="h-full w-full object-cover">
                      </div>
                      <div v-else class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
                        {{ getInitials(admin.full_name) }}
                      </div>
                    </div>
                    <div class="min-w-0 flex-1">
                      <div class="text-sm font-medium text-slate-900 truncate">{{ admin.full_name }}</div>
                      <div class="text-sm text-slate-500 truncate">{{ admin.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4">
                  <span 
                    :class="getRoleBadgeColor(admin.role)"
                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                  >
                    {{ admin.role }}
                  </span>
                </td>
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4">
                  <span 
                    :class="admin.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                  >
                    <span 
                      :class="admin.is_active ? 'bg-green-400' : 'bg-red-400'"
                      class="mr-1.5 h-1.5 w-1.5 rounded-full"
                    />
                    {{ admin.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4">
                  <div class="text-sm text-slate-900">{{ admin.creator?.full_name || 'System' }}</div>
                </td>
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4">
                  <div class="text-sm text-slate-500">{{ formatDate(admin.last_login_at) }}</div>
                </td>
                <td class="whitespace-nowrap px-4 xl:px-6 py-3 xl:py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      @click="toggleStatus(admin)"
                      :class="admin.is_active ? 'text-orange-600 hover:text-orange-900' : 'text-green-600 hover:text-green-900'"
                      class="text-sm font-medium"
                      :title="admin.is_active ? 'Deactivate' : 'Activate'"
                    >
                      {{ admin.is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                    <button
                      @click="openEditModal(admin)"
                      class="text-sm font-medium text-blue-600 hover:text-blue-900"
                    >
                      Edit
                    </button>
                    <button
                      @click="openDeleteModal(admin)"
                      class="text-sm font-medium text-red-600 hover:text-red-900"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!admins.data.length">
                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                  No admins found
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="admins.links.length > 3" class="border-t border-slate-200 bg-white px-4 py-3 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="text-sm text-slate-700">
              Showing <span class="font-medium">{{ admins.from }}</span> to <span class="font-medium">{{ admins.to }}</span> of <span class="font-medium">{{ admins.total }}</span> results
            </div>
            <div class="flex gap-1">
              <Link
                v-for="(link, index) in admins.links"
                :key="index"
                :href="link.url"
                :class="[
                  'px-3 py-2 text-sm font-medium rounded-lg',
                  link.active
                    ? 'bg-slate-900 text-white'
                    : link.url
                    ? 'text-slate-700 hover:bg-slate-100'
                    : 'text-slate-400 cursor-not-allowed'
                ]"
                :disabled="!link.url"
                v-html="link.label"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Admins List (Mobile) -->
      <div class="lg:hidden space-y-3">
        <div v-for="admin in admins.data" :key="admin.id" class="rounded-xl bg-white p-4 shadow-sm border border-slate-200">
          <div class="flex items-start gap-3 mb-3">
            <div class="h-12 w-12 flex-shrink-0">
              <div v-if="admin.profile_photo_url" class="h-12 w-12 rounded-full overflow-hidden">
                <img :src="admin.profile_photo_url" :alt="admin.full_name" class="h-full w-full object-cover">
              </div>
              <div v-else class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
                {{ getInitials(admin.full_name) }}
              </div>
            </div>
            <div class="min-w-0 flex-1">
              <div class="text-sm font-semibold text-slate-900">{{ admin.full_name }}</div>
              <div class="text-sm text-slate-500 truncate">{{ admin.email }}</div>
              <div class="flex items-center gap-2 mt-1">
                <span 
                  :class="getRoleBadgeColor(admin.role)"
                  class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium capitalize"
                >
                  {{ admin.role }}
                </span>
                <span 
                  :class="admin.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                >
                  {{ admin.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
            </div>
          </div>

          <div class="border-t border-slate-200 pt-3 space-y-2">
            <div class="text-xs text-slate-600">
              <span class="font-medium">Created by:</span> {{ admin.creator?.full_name || 'System' }}
            </div>
            <div class="text-xs text-slate-600">
              <span class="font-medium">Last login:</span> {{ formatDate(admin.last_login_at) }}
            </div>
          </div>

          <div class="flex gap-2 mt-3">
            <button
              @click="toggleStatus(admin)"
              :class="admin.is_active ? 'border-orange-300 text-orange-700 hover:bg-orange-50' : 'border-green-300 text-green-700 hover:bg-green-50'"
              class="flex-1 rounded-lg border px-3 py-2 text-xs font-semibold"
            >
              {{ admin.is_active ? 'Deactivate' : 'Activate' }}
            </button>
            <button
              @click="openEditModal(admin)"
              class="flex-1 rounded-lg border border-blue-300 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-50"
            >
              Edit
            </button>
            <button
              @click="openDeleteModal(admin)"
              class="rounded-lg border border-red-300 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50"
            >
              Delete
            </button>
          </div>
        </div>

        <div v-if="!admins.data.length" class="rounded-xl bg-white p-8 text-center text-sm text-slate-500">
          No admins found
        </div>
      </div>
    </div>

    <!-- Create Admin Modal -->
    <Modal :show="showCreateModal" @close="showCreateModal = false" max-width="2xl">
      <div class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-semibold text-slate-900">Create New Admin</h2>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
          <div class="flex gap-3">
            <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-blue-800">
              <p class="font-medium mb-1">Auto-Generated Password</p>
              <p>A secure password will be automatically generated and sent to the admin's email address.</p>
            </div>
          </div>
        </div>

        <form @submit.prevent="submitCreate" class="space-y-4">
          <div>
            <InputLabel for="full_name" value="Full Name" />
            <TextInput
              id="full_name"
              v-model="createForm.full_name"
              type="text"
              class="mt-1 block w-full"
              required
              autofocus
            />
            <InputError :message="createForm.errors.full_name" class="mt-2" />
          </div>

          <div>
            <InputLabel for="email" value="Email Address" />
            <TextInput
              id="email"
              v-model="createForm.email"
              type="email"
              class="mt-1 block w-full"
              required
            />
            <InputError :message="createForm.errors.email" class="mt-2" />
          </div>

          <div>
            <InputLabel for="role" value="Role" />
            <select
              id="role"
              v-model="createForm.role"
              class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            >
              <option value="admin">Admin</option>
              <option value="superadmin">Superadmin</option>
              <option value="moderator">Moderator</option>
            </select>
            <InputError :message="createForm.errors.role" class="mt-2" />
            <p class="mt-1 text-xs text-slate-500">
              <span class="font-medium">Admin:</span> Standard access | 
              <span class="font-medium">Superadmin:</span> Full access | 
              <span class="font-medium">Moderator:</span> Limited access
            </p>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4">
            <SecondaryButton @click="showCreateModal = false" type="button">
              Cancel
            </SecondaryButton>
            <PrimaryButton :disabled="createForm.processing">
              {{ createForm.processing ? 'Creating...' : 'Create Admin' }}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Edit Admin Modal -->
    <Modal :show="showEditModal" @close="showEditModal = false" max-width="2xl">
      <div class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-semibold text-slate-900">Edit Admin</h2>
          <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitEdit" class="space-y-4">
          <div>
            <InputLabel for="edit_full_name" value="Full Name" />
            <TextInput
              id="edit_full_name"
              v-model="editForm.full_name"
              type="text"
              class="mt-1 block w-full"
              required
            />
            <InputError :message="editForm.errors.full_name" class="mt-2" />
          </div>

          <div>
            <InputLabel for="edit_email" value="Email Address" />
            <TextInput
              id="edit_email"
              v-model="editForm.email"
              type="email"
              class="mt-1 block w-full"
              required
            />
            <InputError :message="editForm.errors.email" class="mt-2" />
          </div>

          <div>
            <InputLabel for="edit_role" value="Role" />
            <select
              id="edit_role"
              v-model="editForm.role"
              class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            >
              <option value="admin">Admin</option>
              <option value="superadmin">Superadmin</option>
              <option value="moderator">Moderator</option>
            </select>
            <InputError :message="editForm.errors.role" class="mt-2" />
          </div>

          <div>
            <label class="flex items-center">
              <input
                type="checkbox"
                v-model="editForm.is_active"
                class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500"
              />
              <span class="ml-2 text-sm text-slate-600">Active (can login)</span>
            </label>
            <InputError :message="editForm.errors.is_active" class="mt-2" />
          </div>

          <div class="flex items-center justify-end gap-3 pt-4">
            <SecondaryButton @click="showEditModal = false" type="button">
              Cancel
            </SecondaryButton>
            <PrimaryButton :disabled="editForm.processing">
              {{ editForm.processing ? 'Updating...' : 'Update Admin' }}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Delete Admin Modal -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false" max-width="md">
      <div class="p-6">
        <div class="flex items-start gap-4 mb-6">
          <div class="rounded-full bg-red-100 p-3">
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <div class="flex-1">
            <h2 class="text-xl font-semibold text-slate-900 mb-2">Delete Admin</h2>
            <p class="text-sm text-slate-600">
              Are you sure you want to delete <span class="font-semibold">{{ selectedAdmin?.full_name }}</span>? 
              This action cannot be undone.
            </p>
          </div>
        </div>

        <div class="flex items-center justify-end gap-3">
          <SecondaryButton @click="showDeleteModal = false">
            Cancel
          </SecondaryButton>
          <DangerButton @click="deleteAdmin">
            Delete Admin
          </DangerButton>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Ensure proper mobile responsiveness */
@media (max-width: 640px) {
  .space-y-4 {
    padding: 1rem;
  }
}
</style>
