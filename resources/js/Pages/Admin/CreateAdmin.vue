<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
  full_name: '',
  email: '',
  role: 'admin',
});

const submit = () => {
  form.post(route('admin.admins.store'));
};
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Create Admin" />

    <section class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-semibold text-slate-900">Create Admin</h1>
          <p class="mt-1 text-sm text-slate-600">Add a new admin to the system</p>
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
              placeholder="John Doe"
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
              placeholder="john@example.com"
            />
            <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
            <p class="mt-1 text-xs text-slate-500">Login credentials will be sent to this email</p>
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
            
            <!-- Role descriptions -->
            <div class="mt-3 space-y-2">
              <div class="flex items-start gap-2 text-xs text-slate-600">
                <span class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-purple-800 font-semibold">Super Admin</span>
                <p>Full access - Can create/manage admins, access all features</p>
              </div>
              <div class="flex items-start gap-2 text-xs text-slate-600">
                <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-blue-800 font-semibold">Admin</span>
                <p>Standard access - Can manage content and users</p>
              </div>
              <div class="flex items-start gap-2 text-xs text-slate-600">
                <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-green-800 font-semibold">Moderator</span>
                <p>Limited access - Can review and moderate content</p>
              </div>
            </div>
          </div>

          <!-- Info Box -->
          <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
            <div class="flex gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-600 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
              </svg>
              <div class="text-sm text-blue-900">
                <p class="font-semibold mb-1">Account Creation Process</p>
                <ul class="list-disc list-inside space-y-1 text-xs text-blue-800">
                  <li>A secure random password will be generated automatically</li>
                  <li>Login credentials will be sent to the admin's email</li>
                  <li>Admin must verify with OTP on first login</li>
                  <li>Admin can change password after logging in</li>
                </ul>
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
              {{ form.processing ? 'Creating Admin...' : 'Create Admin' }}
            </button>
          </div>
        </form>
      </div>
    </section>
  </AuthenticatedLayout>
</template>
