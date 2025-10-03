<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { reactive, ref, computed, onMounted } from "vue";

defineProps({
  status: {
    type: String,
  },
});

const form = useForm({
  oldPassword: "",
  newPassword: "",
  errors: {},
  processing: false,
});

const status = ref(null);
const showPassword = ref(false);
function submit() {
  form.processing = true;
  form.post(route("login"), {
    onFinish: () => form.reset(["oldPassword", "newPassword"]),
  });
  setTimeout(() => {
    form.processing = false;
  }, 3000);
}
onMounted(() => {
  status.value = form.errors.oldPassword || form.errors.newPassword;
});
</script>

<style>
.labelClass {
  color: #09518c;
  font-weight: 900;
  font-size: 16px;
}

.AccessBgBlue {
  background-color: #09518c !important;
  color: #fff;
}
.AccessBgGreen {
  background-color: #8dc63f !important;
  color: #fff;
}
.AccessBlue {
  color: #09518c !important;
}

/* Forgot Password Link */
.forgot-password-link {
  color: #8B4513;
  opacity: 0.75;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 0.875rem;
  font-weight: 500;
  text-decoration: none;
  position: relative;
  display: inline-block;
}

.forgot-password-link:hover {
  opacity: 1;
  color: #09518c;
  transform: translateY(-1px);
}

.forgot-password-link:hover::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent, #09518c, transparent);
  animation: underline-expand 0.3s ease-out;
}

@keyframes underline-expand {
  from {
    transform: scaleX(0);
  }
  to {
    transform: scaleX(1);
  }
}

/* Mobile-specific improvements */
@media (max-width: 640px) {
  /* Ensure touch targets are at least 44px for better accessibility */
  .forgot-password-link {
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8px 12px;
  }
  
  /* Improve form spacing on mobile */
  .space-y-3 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 1rem;
  }
}
</style>
<template>
  <GuestLayout>
    <Head title="PAL Users Login" />

    <form @submit.prevent="submit" class="space-y-3 sm:space-y-2">
      <div
        v-if="status"
        class="mb-2 text-sm text-green-600 font-medium text-center"
      >
        {{ status }}
      </div>

      <!-- Old Password -->
      <div>
        <InputLabel class="text-[#3F444D] text-sm font-medium" for="oldPassword" value="Old Password" />
        <TextInput
          id="oldPassword"
          type="text"
          class="mt-1 block w-full text-sm sm:text-base"
          v-model="form.oldPassword"
          required
          autofocus
          placeholder="************"
          autocomplete="old-password"
        />
        <InputError class="mt-2" :message="form.errors.oldPassword" />
      </div>

      <!-- New Password -->
      <div>
        <InputLabel class="text-[#3F444D] text-sm font-medium" for="newPassword" value="New Password" />
        <div class="relative">
          <TextInput
            id="newPassword"
            :type="showPassword ? 'text' : 'password'"
            class="mt-1 block w-full pr-10 text-sm sm:text-base"
            v-model="form.newPassword"
            required
            placeholder="************"
            autocomplete="new-password"
          />
          <button
            type="button"
            class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 rounded p-1 transition-all duration-200"
            @click="showPassword = !showPassword"
          >
            <!-- 👁 Show Eye Icon when password is hidden -->
            <svg
              v-if="!showPassword"
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
              />
            </svg>

            <!-- 🙈 Eye-Off Icon when password is visible -->
            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10 0-.65.058-1.28.175-1.875m3.525-2.85A9.963 9.963 0 0112 4c5.523 0 10 4.477 10 10 0 2.15-.68 4.135-1.825 5.775M15 12a3 3 0 11-6 0 3 3 0 016 0z"
              />
              <line
                x1="3"
                y1="3"
                x2="21"
                y2="21"
                stroke="currentColor"
                stroke-width="2"
              />
            </svg>
          </button>
        </div>
        <InputError class="mt-2" :message="form.errors.newPassword" />
        
      </div>

      <!-- Submit Button -->
      <div class="w-full pt-3 sm:pt-2">
        <PrimaryButton
          class="AccessBgGreen px-6 w-full flex items-center justify-center h-10 sm:h-12 text-sm sm:text-base font-medium"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          <span v-if="form.processing">Processing...</span>
          <span v-else>Change Password</span>
        </PrimaryButton>
      </div>
    </form>
  </GuestLayout>
</template>
