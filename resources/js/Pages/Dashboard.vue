<template>
    <AuthenticatedLayout>
        <Head title="Dashboard" />

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 my-6 sm:my-10">
            <div class="AccessBgOrange text-white rounded-lg shadow p-4 sm:p-5">
                <div>
                    <div class="text-3xl sm:text-4xl font-bold">{{ stats.totalUsers }}</div>
                    <div class="mt-1 text-base sm:text-lg">Total Admin</div>
                </div>
                <div class="flex items-center justify-between mt-3 sm:mt-4">
                    <Link href="user" class="text-xs sm:text-sm hover:text-blue-200">More info</Link>
                    <i class="fas fa-user-shield text-xl sm:text-2xl"></i>
                </div>
            </div>

            <div class="AccessBgGreen text-white rounded-lg shadow p-4 sm:p-5">
                <div>
                    <div class="text-3xl sm:text-4xl font-bold">{{ stats.totalOffices }}</div>
                    <div class="mt-1 text-base sm:text-lg">Total Offices</div>
                </div>
                <div class="flex items-center justify-between mt-3 sm:mt-4">
                    <Link href="branch" class="text-xs sm:text-sm underline hover:text-purple-200">More info</Link>
                    <i class="fas fa-building text-xl sm:text-2xl"></i>
                </div>
            </div>

            <div class="AccessBgBlue text-white rounded-lg shadow p-4 sm:p-5">
                <div>
                    <div class="text-3xl sm:text-4xl font-bold">{{ stats.visitorsToday }}</div>
                    <div class="mt-1 text-base sm:text-lg">Visitors Today</div>
                </div>
                <div class="flex items-center justify-between mt-3 sm:mt-4">
                    <Link href="report/visitors" class="text-xs sm:text-sm underline hover:text-green-200">More info</Link>
                    <i class="fas fa-user-check text-xl sm:text-2xl"></i>
                </div>
            </div>

            <div class="bg-red-500 text-white rounded-lg shadow p-4 sm:p-5">
                <div>
                    <div class="text-3xl sm:text-4xl font-bold">{{ stats.staffClockedInToday }}</div>
                    <div class="mt-1 text-base sm:text-lg">Staff Today</div>
                </div>
                <div class="flex items-center justify-between mt-3 sm:mt-4">
                    <Link href="report/staff" class="text-xs sm:text-sm underline hover:text-red-200">More info</Link>
                    <i class="fas fa-user-tie text-xl sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
            <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                <h2 class="text-lg sm:text-xl font-semibold mb-3 sm:mb-4">Recent Visitors</h2>
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <ul class="min-w-full px-4 sm:px-0">
                        <li v-for="visitor in stats.recentVisitors" :key="visitor.id" class="border-b py-2 text-sm sm:text-base">
                            <span class="font-semibold">{{ visitor.last_name }}</span> - <span class="text-xs sm:text-sm text-slate-600">{{ formatDate(visitor.clock_in) }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                <h2 class="text-lg sm:text-xl font-semibold mb-3 sm:mb-4">Recent Staff Clock-ins</h2>
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <ul class="min-w-full px-4 sm:px-0">
                        <li v-for="staff in stats.recentStaff" :key="staff.id" class="border-b py-2 text-sm sm:text-base">
                            <span class="font-semibold">{{ staff.name ?? 'Unknown' }}</span> - <span class="text-xs sm:text-sm text-slate-600">{{ formatDate(staff.created_at) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import {  Head, Link, useForm, usePage, router } from '@inertiajs/vue3'
    import { ref, computed } from 'vue'

    // defineProps(['totalUsers', 'totalVisitors', 'totalOffices', 'staffClocksToday', 'visitorClocksToday']);

    const page = usePage()
    const stats = computed(() => page.props.stats)

    function formatDate(dateStr) {
    const date = new Date(dateStr)
    return date.toLocaleString()
    }
</script>

<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');


.AccessBgOrange {
  background-color: #f58220 !important;
}

.AccessBgGreen {
  background-color: #8dc63f !important;
  color: #fff;
}


.AccessBgBlue {
  background-color: #09518c !important;
  color: #fff;
}
</style>

