<template>
  <div class="fixed bottom-4 right-4 left-4 sm:left-auto z-50 flex flex-col gap-2 pointer-events-none">
    <div
      v-for="toast in toasts"
      :key="toast.id"
      class="pointer-events-auto"
    >
      <Toast
        :message="toast.message"
        :type="toast.type"
        :duration="toast.duration"
        @close="removeToast(toast.id)"
      />
    </div>
  </div>
</template>

<script setup>
import { useToast } from '@/Composables/useToast';
import Toast from './Toast.vue';

const { toasts, remove } = useToast();

const removeToast = (id) => {
  remove(id);
};
</script>
