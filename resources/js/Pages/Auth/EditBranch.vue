<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { useForm, Head } from '@inertiajs/vue3'

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
})

// Initialize form with existing user data
const form = useForm({
  name: props.user.name || '',
  email: props.user.email || '',
  password: 'password', // Default password if not changing
})

// Submit updated branch data
const submit = () => {
  form.patch(route('update.branch', props.user.id), {
    preserveScroll: true,
    onSuccess: () => {
      // You can add a toast notification here
      alert('Branch updated successfully!')
    },
    onError: () => {
      // Errors are automatically handled by Inertia and mapped to form.errors
      console.log('Validation failed')
    },
  })
}
</script>

<template>
  <div class="hidden"></div>
</template>
