<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  email: String,
});

const form = useForm({
  otp: '',
});

const isLoading = ref(false);
const canResend = ref(false);
const resendCooldown = ref(60);
let cooldownInterval = null;

// OTP input refs
const otpInputs = ref([]);

const startCooldown = () => {
  canResend.value = false;
  resendCooldown.value = 60;
  
  cooldownInterval = setInterval(() => {
    resendCooldown.value--;
    
    if (resendCooldown.value <= 0) {
      clearInterval(cooldownInterval);
      canResend.value = true;
    }
  }, 1000);
};

onMounted(() => {
  startCooldown();
  // Focus first input
  if (otpInputs.value[0]) {
    otpInputs.value[0].focus();
  }
});

onUnmounted(() => {
  if (cooldownInterval) {
    clearInterval(cooldownInterval);
  }
});

const handleOtpInput = (index, event) => {
  const value = event.target.value;
  
  // Only allow numbers
  if (!/^\d*$/.test(value)) {
    event.target.value = '';
    return;
  }
  
  // Move to next input if digit entered
  if (value && index < 5) {
    otpInputs.value[index + 1]?.focus();
  }
  
  // Update form value
  updateOtpValue();
};

const handleKeyDown = (index, event) => {
  // Move to previous input on backspace
  if (event.key === 'Backspace' && !event.target.value && index > 0) {
    otpInputs.value[index - 1]?.focus();
  }
};

const handlePaste = (event) => {
  event.preventDefault();
  const pastedData = event.clipboardData.getData('text').slice(0, 6);
  
  if (!/^\d+$/.test(pastedData)) {
    return;
  }
  
  // Fill inputs with pasted data
  pastedData.split('').forEach((digit, index) => {
    if (otpInputs.value[index]) {
      otpInputs.value[index].value = digit;
    }
  });
  
  // Focus last filled input
  const lastIndex = Math.min(pastedData.length - 1, 5);
  otpInputs.value[lastIndex]?.focus();
  
  updateOtpValue();
};

const updateOtpValue = () => {
  form.otp = otpInputs.value.map(input => input?.value || '').join('');
};

const submit = async () => {
  if (isLoading.value || form.otp.length !== 6) return;
  
  isLoading.value = true;
  form.clearErrors();
  
  axios.post(route('admin.otp.verify'), {
    otp: form.otp,
  })
  .then(response => {
    if (response.data.success && response.data.redirect) {
      window.location.href = response.data.redirect;
    }
  })
  .catch(error => {
    isLoading.value = false;
    
    if (error.response && error.response.data && error.response.data.errors) {
      Object.keys(error.response.data.errors).forEach(key => {
        form.setError(key, error.response.data.errors[key][0]);
      });
    } else {
      form.setError('otp', 'An error occurred. Please try again.');
    }
    
    // Clear inputs on error
    otpInputs.value.forEach(input => {
      if (input) input.value = '';
    });
    form.otp = '';
    otpInputs.value[0]?.focus();
  });
};

const resendOtp = async () => {
  if (!canResend.value) return;
  
  form.clearErrors();
  
  axios.post(route('admin.otp.resend'))
  .then(response => {
    startCooldown();
    // Show success message (you can add a toast notification here)
  })
  .catch(error => {
    if (error.response && error.response.data && error.response.data.errors) {
      Object.keys(error.response.data.errors).forEach(key => {
        form.setError(key, error.response.data.errors[key][0]);
      });
    }
  });
};

const backToLogin = () => {
  router.visit(route('admin.login'));
};
</script>

<template>
  <GuestLayout>
    <Head title="Verify OTP" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="text-center space-y-2">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/10 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-white">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-white">Check your email</h2>
        <p class="text-sm text-white/70">
          We sent a verification code to<br>
          <span class="font-semibold text-white">{{ email }}</span>
        </p>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- OTP Input -->
        <div class="space-y-3">
          <label class="block text-xs font-semibold uppercase tracking-wide text-white/70 text-center">
            Enter 6-digit code
          </label>
          
          <div class="flex justify-center gap-2">
            <input
              v-for="index in 6"
              :key="index"
              :ref="el => otpInputs[index - 1] = el"
              type="text"
              maxlength="1"
              inputmode="numeric"
              pattern="\d*"
              :disabled="isLoading"
              @input="handleOtpInput(index - 1, $event)"
              @keydown="handleKeyDown(index - 1, $event)"
              @paste="handlePaste"
              class="w-12 h-14 text-center text-2xl font-bold rounded-xl border border-white/20 bg-white/10 text-white focus:border-white/60 focus:outline-none focus:ring-2 focus:ring-white/30 disabled:opacity-50"
            />
          </div>
          
          <p v-if="form.errors.otp" class="text-xs text-rose-300 text-center">{{ form.errors.otp }}</p>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="isLoading || form.otp.length !== 6"
          class="w-full bg-white text-black rounded-xl px-4 py-3 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-white/60 disabled:opacity-60"
        >
          {{ isLoading ? 'Verifying…' : 'Verify & Login' }}
        </button>

        <!-- Resend OTP -->
        <div class="text-center space-y-2">
          <button
            type="button"
            @click="resendOtp"
            :disabled="!canResend"
            class="text-sm text-white/70 hover:text-white transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ canResend ? 'Resend code' : `Resend in ${resendCooldown}s` }}
          </button>
          
          <div>
            <button
              type="button"
              @click="backToLogin"
              class="text-xs text-white/50 hover:text-white/70 transition"
            >
              ← Back to login
            </button>
          </div>
        </div>

        <!-- Info -->
        <div class="text-center">
          <p class="text-xs text-white/50">
            ⏱️ Code expires in 5 minutes
          </p>
        </div>
      </form>
    </div>
  </GuestLayout>
</template>
