<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const page = usePage();
const admin = computed(() => page.props.admin ?? page.props.auth?.user ?? {});

// Profile image upload
const profileImageInput = ref(null);
const profileImagePreview = ref(admin.value?.profile_image_url || null);

const profileImageForm = useForm({
  profile_image: null,
});

const selectProfileImage = () => {
  profileImageInput.value?.click();
};

const handleProfileImageChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    profileImageForm.profile_image = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      profileImagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
    uploadProfileImage();
  }
};

const uploadProfileImage = () => {
  profileImageForm.post(route('admin.profile.upload-image'), {
    preserveScroll: true,
    onSuccess: () => {
      profileImageForm.reset();
    },
  });
};

// Profile update form
const profileForm = useForm({
  display_name: admin.value?.name || '',
  email: admin.value?.email || '',
  user_name: admin.value?.username || '',
  phone: admin.value?.phone || '',
});

const updateProfile = () => {
  profileForm.post(route('admin.profile.update'), {
    preserveScroll: true,
  });
};

// Password change form
const showPasswordSection = ref(false);
const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const updatePassword = () => {
  passwordForm.post(route('admin.profile.update-password'), {
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset();
      showPasswordSection.value = false;
    },
  });
};

const togglePasswordSection = () => {
  showPasswordSection.value = !showPasswordSection.value;
  if (!showPasswordSection.value) {
    passwordForm.reset();
  }
};
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Profile" />

    <section class="grid gap-6 lg:grid-cols-3">
      <!-- Profile Card -->
      <div class="lg:col-span-1 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
        <div class="flex flex-col items-center text-center">
          <!-- Profile Image -->
          <div class="relative">
            <div v-if="profileImagePreview" class="h-24 w-24 overflow-hidden rounded-full ring-4 ring-slate-100">
              <img :src="profileImagePreview" alt="Profile" class="h-full w-full object-cover" />
            </div>
            <div v-else class="flex h-24 w-24 items-center justify-center rounded-full cmBgDark text-2xl font-semibold text-white ring-4 ring-slate-100">
              {{ (admin?.name || admin?.email || 'CM').substring(0, 2).toUpperCase() }}
            </div>

            <!-- Upload Button -->
            <button
              @click="selectProfileImage"
              :disabled="profileImageForm.processing"
              type="button"
              class="absolute bottom-0 right-0 flex h-8 w-8 items-center justify-center rounded-full bg-slate-900 text-white shadow-lg hover:bg-slate-800 disabled:opacity-50"
              title="Upload profile image"
            >
              <svg v-if="!profileImageForm.processing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 animate-spin">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
              </svg>
            </button>

            <input
              ref="profileImageInput"
              type="file"
              accept="image/*"
              class="hidden"
              @change="handleProfileImageChange"
            />
          </div>

          <h2 class="mt-4 text-xl font-semibold text-slate-900">{{ admin?.name }}</h2>
          <p class="mt-1 text-sm font-medium text-slate-600 capitalize">{{ admin?.role ?? 'admin' }}</p>
          <p class="mt-3 text-xs text-slate-400">Member since {{ admin?.joined_at || '—' }}</p>
        </div>
      </div>

      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Personal Details -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-900">Personal Details</h3>
          </div>

          <form @submit.prevent="updateProfile" class="mt-6 space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label for="display_name" class="block text-sm font-medium text-slate-700">Full Name</label>
                <input
                  id="display_name"
                  v-model="profileForm.display_name"
                  type="text"
                  required
                  class="mt-1 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
                />
                <p v-if="profileForm.errors.display_name" class="mt-1 text-xs text-rose-600">{{ profileForm.errors.display_name }}</p>
              </div>

              <div>
                <label for="user_name" class="block text-sm font-medium text-slate-700">Username</label>
                <input
                  id="user_name"
                  v-model="profileForm.user_name"
                  type="text"
                  required
                  class="mt-1 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
                />
                <p v-if="profileForm.errors.user_name" class="mt-1 text-xs text-rose-600">{{ profileForm.errors.user_name }}</p>
              </div>

              <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email Address</label>
                <input
                  id="email"
                  v-model="profileForm.email"
                  type="email"
                  required
                  class="mt-1 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
                />
                <p v-if="profileForm.errors.email" class="mt-1 text-xs text-rose-600">{{ profileForm.errors.email }}</p>
              </div>

              <div>
                <label for="phone" class="block text-sm font-medium text-slate-700">Phone Number</label>
                <input
                  id="phone"
                  v-model="profileForm.phone"
                  type="text"
                  class="mt-1 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
                />
                <p v-if="profileForm.errors.phone" class="mt-1 text-xs text-rose-600">{{ profileForm.errors.phone }}</p>
              </div>
            </div>

            <div class="flex items-center justify-between pt-2">
              <p v-if="profileForm.recentlySuccessful" class="text-sm text-emerald-600">Profile updated successfully!</p>
              <button
                type="submit"
                :disabled="profileForm.processing"
                class="ml-auto rounded-xl cmBgDark px-6 py-2.5 text-sm font-semibold text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-slate-400 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <svg v-if="profileForm.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ profileForm.processing ? 'Saving...' : 'Save Changes' }}</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Password Change Section -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-semibold text-slate-900">Security & Password</h3>
              <p class="mt-1 text-sm text-slate-500">Keep your account secure by updating your password regularly</p>
            </div>
            <button
              type="button"
              @click="togglePasswordSection"
              class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
              {{ showPasswordSection ? 'Cancel' : 'Change Password' }}
            </button>
          </div>

          <form v-if="showPasswordSection" @submit.prevent="updatePassword" class="mt-6 space-y-4">
            <div>
              <label for="current_password" class="block text-sm font-medium text-slate-700">Current Password</label>
              <input
                id="current_password"
                v-model="passwordForm.current_password"
                type="password"
                required
                class="mt-1 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
              />
              <p v-if="passwordForm.errors.current_password" class="mt-1 text-xs text-rose-600">{{ passwordForm.errors.current_password }}</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label for="password" class="block text-sm font-medium text-slate-700">New Password</label>
                <input
                  id="password"
                  v-model="passwordForm.password"
                  type="password"
                  required
                  class="mt-1 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
                />
                <p v-if="passwordForm.errors.password" class="mt-1 text-xs text-rose-600">{{ passwordForm.errors.password }}</p>
              </div>

              <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm New Password</label>
                <input
                  id="password_confirmation"
                  v-model="passwordForm.password_confirmation"
                  type="password"
                  required
                  class="mt-1 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
                />
              </div>
            </div>

            <div class="flex items-center justify-between pt-2">
              <p v-if="passwordForm.recentlySuccessful" class="text-sm text-emerald-600">Password updated successfully!</p>
              <button
                type="submit"
                :disabled="passwordForm.processing"
                class="ml-auto rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <svg v-if="passwordForm.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ passwordForm.processing ? 'Updating...' : 'Update Password' }}</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Account Information -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <h3 class="text-lg font-semibold text-slate-900">Account Information</h3>
          <dl class="mt-6 grid gap-4 sm:grid-cols-2 text-sm">
            <div>
              <dt class="text-slate-500">Role</dt>
              <dd class="mt-1 font-semibold text-slate-900 capitalize">{{ admin?.role ?? 'admin' }}</dd>
            </div>
            <div>
              <dt class="text-slate-500">Last Login</dt>
              <dd class="mt-1 font-medium text-slate-900">{{ admin?.last_login_at || '—' }}</dd>
            </div>
            <div>
              <dt class="text-slate-500">Account Status</dt>
              <dd class="mt-1">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                  <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                  Active
                </span>
              </dd>
            </div>
            <div>
              <dt class="text-slate-500">Member Since</dt>
              <dd class="mt-1 font-medium text-slate-900">{{ admin?.joined_at || '—' }}</dd>
            </div>
          </dl>
        </div>
      </div>
    </section>
  </AuthenticatedLayout>
</template>
