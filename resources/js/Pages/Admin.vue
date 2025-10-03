<template>
    <AuthenticatedLayout>
        <Head title="Dashboard" />

        <div class="grid grid-cols-4 gap-4 my-10">
            <div class="AccessBgOrange text-white rounded shadow p-5">
                <div>
                    <div class="text-4xl font-bold">{{ stats.totalUsers }}</div>
                    <div class="mt-1 text-lg">Total Admin</div>
                </div>
                <div class="flex items-center justify-between mt-4">
                    <Link href="user" class="text-sm hover:text-blue-200">More info</Link>
                    <i class="fas fa-user-shield text-2xl"></i>
                </div>
            </div>

            <div class="AccessBgGreen text-white rounded shadow p-5">
                <div>
                    <div class="text-4xl font-bold">{{ stats.totalOffices }}</div>
                    <div class="mt-1 text-lg">Total Offices</div>
                </div>
                <div class="flex items-center justify-between mt-4">
                    <Link href="branch" class="text-sm underline hover:text-purple-200">More info</Link>
                    <i class="fas fa-building text-2xl"></i>
                </div>
            </div>

            <div class="AccessBgBlue text-white rounded shadow p-5">
                <div>
                    <div class="text-4xl font-bold">{{ stats.visitorsToday }}</div>
                    <div class="mt-1 text-lg">Visitors Today</div>
                </div>
                <div class="flex items-center justify-between mt-4">
                    <Link href="report/visitors" class="text-sm underline hover:text-green-200">More info</Link>
                    <i class="fas fa-user-check text-2xl"></i>
                </div>
            </div>

            <div class="bg-red-500 text-white rounded shadow p-5">
                <div>
                    <div class="text-4xl font-bold">{{ stats.staffClockedInToday }}</div>
                    <div class="mt-1 text-lg">Staff Today</div>
                </div>
                <div class="flex items-center justify-between mt-4">
                    <Link href="report/staff" class="text-sm underline hover:text-red-200">More info</Link>
                    <i class="fas fa-user-tie text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-xl font-semibold mb-4">Recent Visitors</h2>
                <ul>
                    <li v-for="visitor in stats.recentVisitors" :key="visitor.id" class="border-b py-2">
                        <span class="font-semibold">{{ visitor.last_name }}</span> - {{ formatDate(visitor.clock_in) }}
                    </li>
                </ul>
            </div>

            <div class="bg-white rounded shadow p-4">
                <h2 class="text-xl font-semibold mb-4">Recent Staff Clock-ins</h2>
                <ul>
                <li v-for="staff in stats.recentStaff" :key="staff.id" class="border-b py-2">
                    <span class="font-semibold">{{ staff.name ?? 'Unknown' }}</span> - {{ formatDate(staff.created_at) }}
                </li>
                </ul>
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
