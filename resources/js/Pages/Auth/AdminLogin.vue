<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { ref } from 'vue';

const form = useForm({
  email: '',
  password: '',
});

const showPassword = ref(false);
const isLoading = ref(false);

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};

const submit = async () => {
  if (isLoading.value) return;
  
  isLoading.value = true;
  
  // Clear previous errors
  form.clearErrors();
  
  // Send POST request
  axios.post(route('admin.login'), {
    email: form.email,
    password: form.password,
  })
  .then(response => {
    // Success - redirect to OTP verify page
    if (response.data.success) {
      router.visit(route('admin.otp.verify'));
    }
  })
  .catch(error => {
    isLoading.value = false;
    
    // Handle validation errors
    if (error.response && error.response.data && error.response.data.errors) {
      Object.keys(error.response.data.errors).forEach(key => {
        form.setError(key, error.response.data.errors[key][0]);
      });
    } else {
      form.setError('email', 'An error occurred. Please try again.');
    }
  });
};
</script>

<template>
  <GuestLayout>
    <Head title="Admin Login" />

    <form @submit.prevent="submit" class="space-y-6">
      <div class="space-y-2">
        <label for="email" class="text-xs font-semibold uppercase tracking-wide text-white/70">Email</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          required
          autofocus
          :disabled="isLoading"
          class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder-white/40 focus:border-white/60 focus:outline-none focus:ring-2 focus:ring-white/30 disabled:opacity-50"
          placeholder="you@contentmatch.test"
        />
        <p v-if="form.errors.email" class="text-xs text-rose-300">{{ form.errors.email }}</p>
      </div>

      <div class="space-y-2">
        <label for="password" class="text-xs font-semibold uppercase tracking-wide text-white/70">Password</label>
        <div class="relative">
          <input
            id="password"
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            required
            :disabled="isLoading"
            class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 pr-12 text-sm text-white placeholder-white/40 focus:border-white/60 focus:outline-none focus:ring-2 focus:ring-white/30 disabled:opacity-50"
            placeholder="Enter your password"
          />
          <button
            type="button"
            @click="togglePasswordVisibility"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-white/50 hover:text-white/80 focus:outline-none transition"
            tabindex="-1"
            :disabled="isLoading"
          >
            <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
            </svg>
          </button>
        </div>
        <p v-if="form.errors.password" class="text-xs text-rose-300">{{ form.errors.password }}</p>
      </div>

      <div class="flex items-center justify-end text-sm text-white/70">
        <span class="text-xs text-white/50">Need help? support@contentmatch.test</span>
      </div>

      <button
        type="submit"
        :disabled="isLoading"
        class="w-full bg-white text-black rounded-xl px-4 py-3 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-white/60 disabled:opacity-60"
      >
        {{ isLoading ? 'Verifying credentials…' : 'Continue to verification' }}
      </button>
      
      <div class="text-center">
        <p class="text-xs text-white/50">
          🔐 You'll receive a verification code via email
        </p>
      </div>
    </form>
  </GuestLayout>
</template>
