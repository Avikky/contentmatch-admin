<template>
  <transition name="toast">
    <div
      v-if="show"
      :class="[
        'flex items-start gap-2 sm:gap-3 rounded-lg px-3 sm:px-4 py-2.5 sm:py-3 shadow-lg w-full sm:max-w-md',
        typeClasses,
      ]"
      role="alert"
    >
      <div class="flex-shrink-0">
        <svg
          v-if="type === 'success'"
          class="h-4 w-4 sm:h-5 sm:w-5"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
            clip-rule="evenodd"
          />
        </svg>
        <svg
          v-else-if="type === 'error'"
          class="h-4 w-4 sm:h-5 sm:w-5"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
            clip-rule="evenodd"
          />
        </svg>
        <svg
          v-else-if="type === 'warning'"
          class="h-4 w-4 sm:h-5 sm:w-5"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
            clip-rule="evenodd"
          />
        </svg>
        <svg
          v-else
          class="h-4 w-4 sm:h-5 sm:w-5"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
            clip-rule="evenodd"
          />
        </svg>
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-xs sm:text-sm font-medium break-words">{{ message }}</p>
      </div>
      <button
        @click="close"
        class="flex-shrink-0 rounded-lg p-0.5 sm:p-1 hover:bg-black/10 focus:outline-none focus:ring-2 focus:ring-offset-2"
        :class="closeButtonClass"
      >
        <span class="sr-only">Close</span>
        <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4" fill="currentColor" viewBox="0 0 20 20">
          <path
            fill-rule="evenodd"
            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
            clip-rule="evenodd"
          />
        </svg>
      </button>
    </div>
  </transition>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';

const props = defineProps({
  message: {
    type: String,
    required: true,
  },
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value),
  },
  duration: {
    type: Number,
    default: 5000,
  },
});

const emit = defineEmits(['close']);

const show = ref(false);
let timeout = null;

const typeClasses = computed(() => {
  switch (props.type) {
    case 'success':
      return 'bg-green-50 text-green-800 border border-green-200';
    case 'error':
      return 'bg-red-50 text-red-800 border border-red-200';
    case 'warning':
      return 'bg-yellow-50 text-yellow-800 border border-yellow-200';
    default:
      return 'bg-blue-50 text-blue-800 border border-blue-200';
  }
});

const closeButtonClass = computed(() => {
  switch (props.type) {
    case 'success':
      return 'focus:ring-green-500';
    case 'error':
      return 'focus:ring-red-500';
    case 'warning':
      return 'focus:ring-yellow-500';
    default:
      return 'focus:ring-blue-500';
  }
});

const close = () => {
  show.value = false;
  if (timeout) {
    clearTimeout(timeout);
  }
  setTimeout(() => {
    emit('close');
  }, 300);
};

const startTimer = () => {
  if (props.duration > 0) {
    timeout = setTimeout(() => {
      close();
    }, props.duration);
  }
};

onMounted(() => {
  show.value = true;
  startTimer();
});

watch(() => props.message, () => {
  if (timeout) {
    clearTimeout(timeout);
  }
  startTimer();
});
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  transform: translateX(100%);
  opacity: 0;
}

.toast-leave-to {
  transform: translateY(20px);
  opacity: 0;
}
</style>
