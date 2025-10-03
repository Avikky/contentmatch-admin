<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, usePage, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const auth = usePage().props.auth;


// User Information Form
const userForm = useForm({
    full_name: user.full_name,
    email: user.email,
    mobile_no: user.mobile_no,
});

// // Company Information Form
// const companyForm = useForm({
//     company_name: user.company_name,
//     region_name: user.region_name,
//     region_code: user.region_code,
//     branch_description: user.branch_description,
//     branch_code: user.branch_code,
// });

// Password Form
const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

// Form submission methods
const updateProfile = () => {
    userForm.patch(route('profile.personal.update'));
};

const updateCompany = () => {
    companyForm.patch(route('profile.company.update'));
};

const updatePassword = () => {
    passwordForm.put(route('profile.password.change'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
        onError: () => {
            if (passwordForm.errors.password) {
                passwordForm.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (passwordForm.errors.current_password) {
                passwordForm.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <Head title="Profile Settings" />

    <AuthenticatedLayout>
        <div class="mb-8">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">Profile Settings</h1>
            <p class="text-gray-600">Manage your account information and security settings</p>
        </div>

        <div class="max-w-4xl space-y-8">
            <!-- User Information Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Personal Information</h2>
                            <p class="text-sm text-gray-600">Update your personal details and contact information</p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="updateProfile" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="name" value="Full Name" class="text-gray-700 font-medium" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-2 block w-full"
                                v-model="userForm.full_name"
                                required
                                autofocus
                                autocomplete="name"
                            />
                            <InputError class="mt-2" :message="userForm.errors.name" />
                        </div>

                  
                        <div>
                            <InputLabel for="pin" value="PIN" class="text-gray-700 font-medium" />
                            <TextInput
                                id="pin"
                                type="text"
                                class="mt-2 block w-full"
                                :value="auth.user.pin"
                                disabled
                            />
                        </div>

                        <div>
                            <InputLabel for="email" value="Email Address" class="text-gray-700 font-medium" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-2 block w-full"
                                v-model="userForm.email"
                                required
                                autocomplete="username"
                            />
                            <InputError class="mt-2" :message="userForm.errors.email" />

                            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="mt-3">
                                <div class="flex items-start space-x-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                    <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="text-sm">
                                        <p class="text-amber-800 font-medium">Email verification required</p>
                                        <p class="text-amber-700 mt-1">
                                            Your email address is unverified.
                                            <Link
                                                :href="route('verification.send')"
                                                method="post"
                                                as="button"
                                                class="underline text-amber-800 hover:text-amber-900 font-medium ml-1"
                                            >
                                                Click here to re-send verification email.
                                            </Link>
                                        </p>
                                    </div>
                                </div>

                                <div
                                    v-show="status === 'verification-link-sent'"
                                    class="mt-3 flex items-start space-x-3 p-3 bg-green-50 border border-green-200 rounded-lg"
                                >
                                    <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-sm text-green-800">A new verification link has been sent to your email address.</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <InputLabel for="mobile_no" value="Mobile Number" class="text-gray-700 font-medium" />
                            <TextInput
                                id="mobile_no"
                                type="tel"
                                class="mt-2 block w-full"
                                v-model="userForm.mobile_no"
                                autocomplete="tel"
                            />
                            <InputError class="mt-2" :message="userForm.errors.mobile_no" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200">
                        <Transition
                            enter-active-class="transition ease-in-out duration-300"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out duration-300"
                            leave-to-class="opacity-0"
                        >
                            <div v-if="userForm.recentlySuccessful" class="flex items-center space-x-2 text-green-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm font-medium">Changes saved successfully</span>
                            </div>
                        </Transition>

                        <PrimaryButton :disabled="userForm.processing" class="px-6">
                            <svg v-if="userForm.processing" class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Save Changes
                        </PrimaryButton>
                    </div>
                </form>
            </div>

            <!-- Company Information Section -->
            <!-- <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Company Information</h2>
                            <p class="text-sm text-gray-600">Update your organization and branch details</p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="updateCompany" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="company_name" value="Company Name" class="text-gray-700 font-medium" />
                            <TextInput
                                id="company_name"
                                type="text"
                                class="mt-2 block w-full"
                                v-model="companyForm.company_name"
                                autocomplete="organization"
                            />
                            <InputError class="mt-2" :message="companyForm.errors.company_name" />
                        </div>

                        <div>
                            <InputLabel for="region_name" value="Region Name" class="text-gray-700 font-medium" />
                            <TextInput
                                id="region_name"
                                type="text"
                                class="mt-2 block w-full"
                                v-model="companyForm.region_name"
                                autocomplete="off"
                            />
                            <InputError class="mt-2" :message="companyForm.errors.region_name" />
                        </div>

                        <div>
                            <InputLabel for="region_code" value="Region Code" class="text-gray-700 font-medium" />
                            <TextInput
                                id="region_code"
                                type="text"
                                class="mt-2 block w-full"
                                v-model="companyForm.region_code"
                                autocomplete="off"
                            />
                            <InputError class="mt-2" :message="companyForm.errors.region_code" />
                        </div>

                        <div>
                            <InputLabel for="branch_description" value="Branch Office" class="text-gray-700 font-medium" />
                            <TextInput
                                id="branch_description"
                                type="text"
                                class="mt-2 block w-full"
                                v-model="companyForm.branch_description"
                                autocomplete="off"
                            />
                            <InputError class="mt-2" :message="companyForm.errors.branch_description" />
                        </div>

                        <div class="md:col-span-2">
                            <InputLabel for="branch_code" value="Branch Code" class="text-gray-700 font-medium" />
                            <TextInput
                                id="branch_code"
                                type="text"
                                class="mt-2 block w-full md:w-1/2"
                                v-model="companyForm.branch_code"
                                autocomplete="off"
                            />
                            <InputError class="mt-2" :message="companyForm.errors.branch_code" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200">
                        <Transition
                            enter-active-class="transition ease-in-out duration-300"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out duration-300"
                            leave-to-class="opacity-0"
                        >
                            <div v-if="companyForm.recentlySuccessful" class="flex items-center space-x-2 text-green-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm font-medium">Company information updated successfully</span>
                            </div>
                        </Transition>

                        <PrimaryButton :disabled="companyForm.processing" class="px-6">
                            <svg v-if="companyForm.processing" class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Update Company Info
                        </PrimaryButton>
                    </div>
                </form>
            </div> -->

            <!-- Change Password Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Security Settings</h2>
                            <p class="text-sm text-gray-600">Update your password to keep your account secure</p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="updatePassword" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <InputLabel for="current_password" value="Current Password" class="text-gray-700 font-medium" />
                            <TextInput
                                id="current_password"
                                ref="currentPasswordInput"
                                v-model="passwordForm.current_password"
                                type="password"
                                class="mt-2 block w-full"
                                autocomplete="current-password"
                            />
                            <InputError :message="passwordForm.errors.current_password" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="password" value="New Password" class="text-gray-700 font-medium" />
                            <TextInput
                                id="password"
                                ref="passwordInput"
                                v-model="passwordForm.password"
                                type="password"
                                class="mt-2 block w-full"
                                autocomplete="new-password"
                            />
                            <InputError :message="passwordForm.errors.password" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="password_confirmation" value="Confirm New Password" class="text-gray-700 font-medium" />
                            <TextInput
                                id="password_confirmation"
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                class="mt-2 block w-full"
                                autocomplete="new-password"
                            />
                            <InputError :message="passwordForm.errors.password_confirmation" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div class="text-sm">
                                <p class="font-medium text-blue-800 mb-1">Password Requirements:</p>
                                <ul class="text-blue-700 space-y-1">
                                    <li>• At least 8 characters long</li>
                                    <li>• Include uppercase and lowercase letters</li>
                                    <li>• Include at least one number</li>
                                    <li>• Include at least one special character</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200">
                        <Transition
                            enter-active-class="transition ease-in-out duration-300"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out duration-300"
                            leave-to-class="opacity-0"
                        >
                            <div v-if="passwordForm.recentlySuccessful" class="flex items-center space-x-2 text-green-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm font-medium">Password updated successfully</span>
                            </div>
                        </Transition>

                        <PrimaryButton :disabled="passwordForm.processing" class="px-6">
                            <svg v-if="passwordForm.processing" class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Update Password
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
