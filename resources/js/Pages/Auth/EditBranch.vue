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
  <AuthenticatedLayout>
    <Head title="Edit Branch" />
    <div class="grid grid-cols-12 min-h-screen">
      <div class="col-span-6 col-start-4 p-6">
        <h2 class="my-2 text-center text-xl font-bold">Edit Branch</h2>

        <form @submit.prevent="submit" class="bg-white p-4 rounded-lg shadow-md">
          <!-- Branch Name -->
          <div>
            <InputLabel for="name" value="Branch Name" />
            <TextInput
              id="name"
              type="text"
              class="mt-1 block w-full"
              v-model="form.name"
              required
              autofocus
              autocomplete="name"
            />
            <InputError class="mt-2" :message="form.errors.name" />
          </div>

          <!-- Branch Email -->
          <div class="mt-4">
            <InputLabel for="email" value="Branch Email" />
            <TextInput
              id="email"
              type="email"
              class="mt-1 block w-full"
              v-model="form.email"
              required
              autocomplete="email"
            />
            <InputError class="mt-2" :message="form.errors.email" />
          </div>

          <!-- Password (optional) -->
          <div class="mt-4">
            <InputLabel for="password" value="Password" />
            <TextInput
              id="password"
              type="password"
              class="mt-1 block w-full"
              v-model="form.password"
              autocomplete="new-password"
            />
            <InputError class="mt-2" :message="form.errors.password" />
          </div>

          <!-- Submit Button -->
          <div class="flex items-center justify-end mt-6">
            <PrimaryButton
              class="ms-4"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              Update Branch
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
