<template>
  <AuthenticatedLayout>
    <Head title="Login Trail" />

    <div class="p-6 space-y-6">
      

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold mb-3">Login Trail</h1>

        <input
          v-model="search"
          type="text"
          placeholder="Search Admins..."
          class="px-4 py-2 border rounded shadow w-1/3"
        />
    </div>

      <!-- Table -->
      <div class="bg-white p-4 rounded-lg shadow-md">
        <EasyDataTable
            :headers="headers"
            :items="filteredLogs"
            :rows-per-page="10"

            :search-value="search"
            :search-field="['name', 'email', 'username', 'user_type_text', 'log_type_text']"
            :loading="loading"
        >
          <template #top>
            <input
              v-model="filters.search"
              placeholder="Search by name or email..."
              class="p-2 mb-2 w-full border rounded-md focus:outline-none focus:ring focus:ring-blue-300"
            />
          </template>
        </EasyDataTable>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import EasyDataTable from 'vue3-easy-data-table'
import dayjs from 'dayjs'

const props = defineProps({
  logs: {
    type: Array,
    default: () => []
  }
})

// Filters
const filters = ref({
  date: '',
  range: '',
  search: ''
})
const search = ref('')
const loading = ref(false)

// Apply filters (reload from backend)
const applyFilters = () => {
  router.get(route('login.trail'), filters.value, {
    preserveState: true,
    preserveScroll: true
  })
}

// Table headers
const headers = [
  { text: 'User Name', value: 'name' },
  { text: 'Email', value: 'email' },
  { text: 'Username', value: 'username' },
  { text: 'User Type', value: 'user_type_text' },
  { text: 'Log Type', value: 'log_type_text' },
  { text: 'Date', value: 'created_at' },
]

// Format logs for table display
const formattedLogs = computed(() =>
  props.logs.map(log => ({
    ...log,
    name: log.user_details?.name ?? 'Unknown',
    username: log.user_details?.username ?? 'Unknown',
    email: log.user_details?.email ?? 'N/A',
    created_at: dayjs(log.date_logged).format('DD MMM, YYYY h:mm A')
  }))
)

// Apply frontend filtering (by date range, today, etc.)
const filteredLogs = computed(() => {
  let logs = formattedLogs.value

  // Filter by search term (name or email)
  if (filters.value.search) {
    const searchTerm = filters.value.search.toLowerCase()
    logs = logs.filter(log =>
      (log.user_name ?? '').toLowerCase().includes(searchTerm) ||
      (log.email ?? '').toLowerCase().includes(searchTerm)
    )
  }

  // Filter by specific date
  if (filters.value.date) {
    logs = logs.filter(log => dayjs(log.created_at).isSame(dayjs(filters.value.date), 'day'))
  }

  // Filter by date range
  if (filters.value.range === 'today') {
    logs = logs.filter(log => dayjs(log.created_at).isSame(dayjs(), 'day'))
  } else if (filters.value.range === 'last_7days') {
    logs = logs.filter(log =>
      dayjs(log.created_at).isAfter(dayjs().subtract(7, 'day'))
    )
  } else if (filters.value.range === 'last_30days') {
    logs = logs.filter(log =>
      dayjs(log.created_at).isAfter(dayjs().subtract(30, 'day'))
    )
  }

  return logs
})

const searchField = 'user_name'
</script>

<style scoped>
.e-data-table {
  overflow-x: auto;
  display: block;
}
</style>

<style>
@import "vue3-easy-data-table/dist/style.css";
</style>
