<template>
  <div class="space-y-4">
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold text-slate-900 mb-4">Toast Notification Demo</h2>
      <p class="text-sm text-slate-600 mb-6">
        Click the buttons below to test different toast notification types.
      </p>
      
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <button
          @click="showSuccess"
          class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium"
        >
          Show Success
        </button>
        
        <button
          @click="showError"
          class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium"
        >
          Show Error
        </button>
        
        <button
          @click="showWarning"
          class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-sm font-medium"
        >
          Show Warning
        </button>
        
        <button
          @click="showInfo"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium"
        >
          Show Info
        </button>
      </div>
      
      <div class="mt-6 pt-6 border-t border-slate-200">
        <button
          @click="showMultiple"
          class="px-6 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition text-sm font-medium"
        >
          Show Multiple Toasts
        </button>
      </div>
    </div>
    
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
      <div class="flex items-start gap-3">
        <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
        </svg>
        <div>
          <h3 class="text-sm font-medium text-blue-900">Automatic Backend Integration</h3>
          <p class="mt-1 text-sm text-blue-700">
            Toasts will also appear automatically when your backend returns flash messages 
            (success or error) or validation errors.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useToast } from '@/Composables/useToast';

const { success, error, warning, info } = useToast();

const showSuccess = () => {
  success('Operation completed successfully! Your changes have been saved.');
};

const showError = () => {
  error('Oops! Something went wrong. Please try again.');
};

const showWarning = () => {
  warning('Please review your information before proceeding.');
};

const showInfo = () => {
  info('Did you know? You can customize toast duration and styling.');
};

const showMultiple = () => {
  success('First notification');
  setTimeout(() => info('Second notification'), 500);
  setTimeout(() => warning('Third notification'), 1000);
  setTimeout(() => error('Fourth notification'), 1500);
};
</script>
