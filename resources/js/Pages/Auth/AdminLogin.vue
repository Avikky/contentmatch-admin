<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { reactive, ref, computed, onMounted } from 'vue';


defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});


const form = useForm({
    username: '',
    password: '',
    isAdmin: 1,
    errors: {},
    processing: false,
})

const status = ref(null)

function submit() {
    form.processing = true
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
    setTimeout(() => {
        form.processing = false
    }, 2000)
}

</script>

<style>
    .labelClass{
        color: #09518c;
        font-weight: 900;
        font-size: 16px;
    }

    .AccessBgBlue {
        background-color: #09518c !important;
        color: #fff;
    }

    .AccessBlue {
        color:  #09518c !important;
    }
</style>
<template>
    <GuestLayout>
        <Head title="Admin Login" />

        <div class="max-w-md mx-auto w-full px-6 py-12 rounded-md">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
                Admin Login
            </h2>

            <div v-if="status" class="mb-4 text-sm text-green-600 font-medium text-center">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Email / Branch Code -->
                <div>
                    <InputLabel
                        class="labelClass"
                        for="email"
                        value="Email"
                    />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.username"
                        required
                        autofocus
                        autocomplete="username"
                    />

                    <InputError class="mt-2" :message="form.errors.username" />
                </div>

                <!-- Password -->
                <div>
                    <InputLabel class="labelClass" for="password" value="Password" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- Toggle Link + Submit -->
                <div class="flex items-center justify-between">
                    <PrimaryButton
                        class="AccessBgBlue px-6"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Processing...</span>
                        <span v-else>Log in</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
