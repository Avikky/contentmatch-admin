<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  admin: Object,
});

const form = useForm({
  full_name: props.admin.full_name,
  email: props.admin.email,
  role: props.admin.role,
  is_active: props.admin.is_active,
});

const submit = () => {
  form.put(route('admin.admins.update', props.admin.id));
};
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Edit Admin" />

    <section class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-semibold text-slate-900">Edit Admin</h1>
          <p class="mt-1 text-sm text-slate-600">Update admin details and permissions</p>
        </div>
        <Link
          :href="route('admin.admins.index')"
          class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
          </svg>
          Back to List
        </Link>
      </div>

      <!-- Form -->
      <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <!-- Admin Info -->
        <div class="flex items-center gap-4 pb-6 mb-6 border-b border-slate-200">
          <img :src="admin.profile_photo_url" :alt="admin.full_name" class="w-16 h-16 rounded-full" />
          <div>
            <p class="text-lg font-semibold text-slate-900">{{ admin.full_name }}</p>
            <p class="text-sm text-slate-600">{{ admin.email }}</p>
          </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
          <!-- Full Name -->
          <div>
            <label for="full_name" class="block text-sm font-semibold text-slate-900 mb-2">
              Full Name <span class="text-red-500">*</span>
            </label>
            <input
              id="full_name"
              v-model="form.full_name"
              type="text"
              required
              class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
            />
            <p v-if="form.errors.full_name" class="mt-1 text-xs text-red-500">{{ form.errors.full_name }}</p>
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-semibold text-slate-900 mb-2">
              Email Address <span class="text-red-500">*</span>
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
            />
            <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
          </div>

          <!-- Role -->
          <div>
            <label for="role" class="block text-sm font-semibold text-slate-900 mb-2">
              Role <span class="text-red-500">*</span>
            </label>
            <select
              id="role"
              v-model="form.role"
              required
              class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
            >
              <option value="admin">Admin</option>
              <option value="superadmin">Super Admin</option>
              <option value="moderator">Moderator</option>
            </select>
            <p v-if="form.errors.role" class="mt-1 text-xs text-red-500">{{ form.errors.role }}</p>
          </div>

          <!-- Status -->
          <div>
            <label class="block text-sm font-semibold text-slate-900 mb-3">
              Account Status <span class="text-red-500">*</span>
            </label>
            <div class="flex items-center gap-6">
              <label class="inline-flex items-center gap-2 cursor-pointer">
                <input
                  type="radio"
                  v-model="form.is_active"
                  :value="true"
                  class="w-4 h-4 text-green-600 focus:ring-green-500"
                />
                <span class="text-sm text-slate-700">Active</span>
              </label>
              <label class="inline-flex items-center gap-2 cursor-pointer">
                <input
                  type="radio"
                  v-model="form.is_active"
                  :value="false"
                  class="w-4 h-4 text-red-600 focus:ring-red-500"
                />
                <span class="text-sm text-slate-700">Inactive</span>
              </label>
            </div>
            <p v-if="form.errors.is_active" class="mt-1 text-xs text-red-500">{{ form.errors.is_active }}</p>
            <p class="mt-2 text-xs text-slate-500">Inactive admins cannot log in to the system</p>
          </div>

          <!-- Warning Box -->
          <div v-if="!form.is_active" class="rounded-lg bg-amber-50 border border-amber-200 p-4">
            <div class="flex gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-amber-600 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
              </svg>
              <div class="text-sm text-amber-900">
                <p class="font-semibold mb-1">Warning</p>
                <p class="text-xs text-amber-800">Deactivating this admin will immediately revoke their access to the system.</p>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200">
            <Link
              :href="route('admin.admins.index')"
              class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition"
            >
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#14092A] to-[#2B0F4A] text-white text-sm font-semibold hover:opacity-90 transition disabled:opacity-60"
            >
              {{ form.processing ? 'Updating Admin...' : 'Update Admin' }}
            </button>
          </div>
        </form>
      </div>
    </section>
  </AuthenticatedLayout>
</template>
