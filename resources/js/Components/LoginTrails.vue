<template>
  <div class="login-trails-container">
    <div class="header">
      <h1>Login Trails</h1>
      <div class="actions">
        <button @click="exportData" class="btn btn-primary">Export</button>
        <button @click="showCleanupModal = true" class="btn btn-warning">Cleanup</button>
      </div>
    </div>

    <!-- Filters -->
    <div class="filters">
      <div class="filter-row">
        <input 
          v-model="filters.search" 
          type="text" 
          placeholder="Search users, IPs, actions..." 
          @input="debouncedFilter"
        />
        
        <select v-model="filters.action" @change="applyFilters">
          <option value="">All Actions</option>
          <option v-for="action in actions" :key="action" :value="action">
            {{ formatAction(action) }}
          </option>
        </select>
        
        <select v-model="filters.success" @change="applyFilters">
          <option value="">All Attempts</option>
          <option value="1">Successful</option>
          <option value="0">Failed</option>
        </select>
        
        <select v-model="filters.days" @change="applyFilters">
          <option value="">All Time</option>
          <option value="1">Last 24 Hours</option>
          <option value="7">Last 7 Days</option>
          <option value="30">Last 30 Days</option>
          <option value="90">Last 90 Days</option>
        </select>
      </div>
    </div>

    <!-- Statistics -->
    <div class="statistics" v-if="statistics">
      <div class="stat-card">
        <h3>{{ statistics.total_logins }}</h3>
        <p>Total Logins</p>
      </div>
      <div class="stat-card">
        <h3>{{ statistics.failed_attempts }}</h3>
        <p>Failed Attempts</p>
      </div>
      <div class="stat-card">
        <h3>{{ statistics.unique_users }}</h3>
        <p>Unique Users</p>
      </div>
      <div class="stat-card">
        <h3>{{ statistics.password_changes }}</h3>
        <p>Password Changes</p>
      </div>
    </div>

    <!-- Trails Table -->
    <div class="table-container">
      <table class="trails-table">
        <thead>
          <tr>
            <th>Time</th>
            <th>User</th>
            <th>Action</th>
            <th>Status</th>
            <th>IP Address</th>
            <th>Device</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="trail in trails.data" :key="trail.id" :class="{ 'failed': !trail.success }">
            <td>{{ formatDate(trail.created_at) }}</td>
            <td>
              <div class="user-info">
                <strong v-if="trail.user">{{ trail.user.name }}</strong>
                <span v-else class="unknown-user">{{ trail.username || 'Unknown' }}</span>
              </div>
            </td>
            <td>
              <span class="action-badge" :class="trail.action">
                {{ formatAction(trail.action) }}
              </span>
            </td>
            <td>
              <span class="status-badge" :class="trail.success ? 'success' : 'failed'">
                {{ trail.success ? 'Success' : 'Failed' }}
              </span>
            </td>
            <td>{{ trail.ip_address }}</td>
            <td>
              <div class="device-info">
                <div>{{ trail.device_type }}</div>
                <small>{{ trail.browser }}</small>
              </div>
            </td>
            <td>
              <button @click="showDetails(trail)" class="btn btn-sm">Details</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="pagination" v-if="trails.last_page > 1">
      <button 
        @click="changePage(trails.current_page - 1)"
        :disabled="trails.current_page === 1"
        class="btn"
      >
        Previous
      </button>
      
      <span class="page-info">
        Page {{ trails.current_page }} of {{ trails.last_page }}
      </span>
      
      <button 
        @click="changePage(trails.current_page + 1)"
        :disabled="trails.current_page === trails.last_page"
        class="btn"
      >
        Next
      </button>
    </div>

    <!-- Detail Modal -->
    <div v-if="selectedTrail" class="modal-overlay" @click="closeDetails">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h3>Trail Details</h3>
          <button @click="closeDetails" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <div class="detail-row">
            <strong>Time:</strong> {{ formatDate(selectedTrail.created_at) }}
          </div>
          <div class="detail-row">
            <strong>User:</strong> {{ selectedTrail.user?.name || selectedTrail.username || 'Unknown' }}
          </div>
          <div class="detail-row">
            <strong>Action:</strong> {{ formatAction(selectedTrail.action) }}
          </div>
          <div class="detail-row">
            <strong>Success:</strong> {{ selectedTrail.success ? 'Yes' : 'No' }}
          </div>
          <div class="detail-row" v-if="selectedTrail.reason">
            <strong>Reason:</strong> {{ selectedTrail.reason }}
          </div>
          <div class="detail-row">
            <strong>IP Address:</strong> {{ selectedTrail.ip_address }}
          </div>
          <div class="detail-row">
            <strong>User Agent:</strong> {{ selectedTrail.user_agent }}
          </div>
          <div class="detail-row">
            <strong>Device:</strong> {{ selectedTrail.device_type }} - {{ selectedTrail.platform }}
          </div>
          <div class="detail-row">
            <strong>Browser:</strong> {{ selectedTrail.browser }}
          </div>
          <div class="detail-row" v-if="selectedTrail.session_id">
            <strong>Session ID:</strong> {{ selectedTrail.session_id }}
          </div>
          <div class="detail-row" v-if="selectedTrail.additional_data">
            <strong>Additional Data:</strong>
            <pre>{{ JSON.stringify(selectedTrail.additional_data, null, 2) }}</pre>
          </div>
        </div>
      </div>
    </div>

    <!-- Cleanup Modal -->
    <div v-if="showCleanupModal" class="modal-overlay" @click="showCleanupModal = false">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h3>Cleanup Old Records</h3>
          <button @click="showCleanupModal = false" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <p>Delete records older than:</p>
          <select v-model="cleanupDays">
            <option value="90">90 days</option>
            <option value="180">6 months</option>
            <option value="365">1 year</option>
            <option value="730">2 years</option>
          </select>
          <div class="modal-actions">
            <button @click="performCleanup" class="btn btn-warning">Delete</button>
            <button @click="showCleanupModal = false" class="btn">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { debounce } from 'lodash';

// Reactive data
const trails = ref({ data: [], current_page: 1, last_page: 1 });
const statistics = ref(null);
const selectedTrail = ref(null);
const showCleanupModal = ref(false);
const cleanupDays = ref(365);
const actions = ref([
  'login', 'logout', 'failed_login', 'password_change', 'password_reset'
]);

const filters = reactive({
  search: '',
  action: '',
  success: '',
  days: '30',
  page: 1
});

// Methods
const fetchTrails = async () => {
  try {
    const params = new URLSearchParams();
    Object.entries(filters).forEach(([key, value]) => {
      if (value) params.append(key, value);
    });

    const response = await fetch(`/api/login-trails?${params}`);
    const data = await response.json();
    
    if (data.success) {
      trails.value = data.data;
    }
  } catch (error) {
    console.error('Error fetching trails:', error);
  }
};

const fetchStatistics = async () => {
  try {
    const days = filters.days || 30;
    const response = await fetch(`/api/login-trails/statistics?days=${days}`);
    const data = await response.json();
    
    if (data.success) {
      statistics.value = data.data;
    }
  } catch (error) {
    console.error('Error fetching statistics:', error);
  }
};

const applyFilters = () => {
  filters.page = 1;
  fetchTrails();
  fetchStatistics();
};

const debouncedFilter = debounce(() => {
  applyFilters();
}, 500);

const changePage = (page) => {
  filters.page = page;
  fetchTrails();
};

const showDetails = (trail) => {
  selectedTrail.value = trail;
};

const closeDetails = () => {
  selectedTrail.value = null;
};

const exportData = async () => {
  try {
    const params = new URLSearchParams();
    Object.entries(filters).forEach(([key, value]) => {
      if (value && key !== 'page') params.append(key, value);
    });

    const response = await fetch(`/admin/login-trails/export?${params}`);
    const data = await response.json();
    
    if (data.success) {
      // Create and download file
      const blob = new Blob([JSON.stringify(data.data, null, 2)], { type: 'application/json' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `login-trails-${new Date().toISOString().split('T')[0]}.json`;
      a.click();
      window.URL.revokeObjectURL(url);
    }
  } catch (error) {
    console.error('Error exporting data:', error);
  }
};

const performCleanup = async () => {
  try {
    const response = await fetch('/admin/login-trails/cleanup', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      },
      body: JSON.stringify({ days_to_keep: cleanupDays.value })
    });
    
    const data = await response.json();
    
    if (data.success) {
      alert(data.message);
      showCleanupModal.value = false;
      fetchTrails();
    }
  } catch (error) {
    console.error('Error cleaning up:', error);
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleString();
};

const formatAction = (action) => {
  return action.split('_').map(word => 
    word.charAt(0).toUpperCase() + word.slice(1)
  ).join(' ');
};

// Initialize
onMounted(() => {
  fetchTrails();
  fetchStatistics();
});
</script>

<style scoped>
.login-trails-container {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.filters {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 5px;
  margin-bottom: 20px;
}

.filter-row {
  display: flex;
  gap: 15px;
  align-items: center;
}

.filter-row input,
.filter-row select {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 3px;
}

.statistics {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
  margin-bottom: 20px;
}

.stat-card {
  background: white;
  padding: 20px;
  border-radius: 5px;
  text-align: center;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-card h3 {
  margin: 0;
  color: #09518c;
  font-size: 2em;
}

.table-container {
  background: white;
  border-radius: 5px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.trails-table {
  width: 100%;
  border-collapse: collapse;
}

.trails-table th,
.trails-table td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.trails-table th {
  background: #f8f9fa;
  font-weight: 600;
}

.trails-table tr.failed {
  background-color: #fff5f5;
}

.action-badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.8em;
  font-weight: 600;
}

.action-badge.login { background: #d1f2eb; color: #00695c; }
.action-badge.logout { background: #e3f2fd; color: #1976d2; }
.action-badge.failed_login { background: #ffebee; color: #c62828; }
.action-badge.password_change { background: #fff3e0; color: #ef6c00; }
.action-badge.password_reset { background: #f3e5f5; color: #7b1fa2; }

.status-badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.8em;
  font-weight: 600;
}

.status-badge.success { background: #d1f2eb; color: #00695c; }
.status-badge.failed { background: #ffebee; color: #c62828; }

.device-info {
  font-size: 0.9em;
}

.device-info small {
  color: #666;
}

.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
}

.btn {
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-primary { background: #09518c; color: white; }
.btn-warning { background: #ff9800; color: white; }
.btn-sm { padding: 4px 8px; font-size: 12px; }

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal {
  background: white;
  border-radius: 5px;
  max-width: 600px;
  max-height: 80vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  border-bottom: 1px solid #eee;
}

.modal-body {
  padding: 20px;
}

.detail-row {
  margin-bottom: 10px;
}

.detail-row pre {
  background: #f8f9fa;
  padding: 10px;
  border-radius: 3px;
  font-size: 12px;
  overflow-x: auto;
}

.modal-actions {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.close-btn {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
}

.user-info .unknown-user {
  color: #666;
  font-style: italic;
}
</style>