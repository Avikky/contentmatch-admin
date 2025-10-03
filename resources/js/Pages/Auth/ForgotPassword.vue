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
  email: {
    type: String,
    default: "",
  },
  error: {
    type: String,
    default: "",
  },
});

const form = useForm({
  email: "",
  errors: {},
  processing: false,
});

const status = ref(null);
const showPassword = ref(false);
function submit() {
  form.processing = true;
  form.post(route("password.email"), {
    onFinish: () => form.reset("email"),
  });
  setTimeout(() => {
    form.processing = false;
  }, 3000);
}

onMounted(() => {
  status.value = form.errors.email;
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

/* Login Link */
.login-link {
  color: #8B4513;
  opacity: 0.75;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 0.875rem;
  font-weight: 500;
  text-decoration: none;
  position: relative;
  display: inline-block;
}

.login-link:hover {
  opacity: 1;
  color: #09518c;
  transform: translateY(-1px);
}

.login-link:hover::after {
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
  .login-link {
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
    <Head title="Reset Password" />

    <form @submit.prevent="submit" class="space-y-3 sm:space-y-2">
        <div
            v-if="status"
            class="mb-2 text-sm text-green-600 font-medium text-center"
        >
        >
            {{ status }}
        </div>

        <!-- Email -->
        <div>
            <InputLabel class="text-[#3F444D] text-sm font-medium" for="email" value="Email" />
            <TextInput
            id="email"
            type="text"
            class="mt-1 block w-full text-sm sm:text-base"
            v-model="form.email"
            required
            autofocus
            placeholder="Email"
            autocomplete="email"
            />
            <InputError class="mt-2" :message="error" />
        </div>
        
        <!--Login Link -->
        <div class="flex justify-end mt-2 sm:mt-3">
          <Link 
            :href="route('login')" 
            class="login-link hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500/20 rounded px-2 py-1 transition-all duration-200 text-xs sm:text-sm"
          >
            Back to Login
          </Link>
        </div>

        <!-- Reset Button -->
        <div class="w-full pt-3 sm:pt-2">
            <PrimaryButton
            class="AccessBgGreen px-6 w-full flex items-center justify-center h-10 sm:h-12 text-sm sm:text-base font-medium"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            >
            <span v-if="form.processing">Processing...</span>
            <span v-else>Reset Password</span>
            </PrimaryButton>
        </div>
    </form>
  </GuestLayout>
</template>
