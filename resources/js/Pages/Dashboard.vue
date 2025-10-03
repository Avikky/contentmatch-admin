<template>
  <AuthenticatedLayout>
    <Head title="Dashboard - PAL Gratuity Fund" />

    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900 mb-2">Dashboard Overview</h1>
      <p class="text-gray-600">Track your gratuity fund performance and contributions</p>
    </div>

    <!-- Simplified Dashboard Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Current Balance -->
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <div class="p-2 bg-lime-100 rounded-lg">
            <svg class="w-6 h-6 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <!-- Naira (₦) Symbol -->
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4v16M16 4v16M8 20l8-16" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 9h16M4 15h16" />
            </svg>
          </div>
          <span class="text-xs text-lime-600 bg-lime-100 px-2 py-1 rounded-full font-medium">+Bal</span>
        </div>
        <div class="space-y-1">
          <p class="text-sm font-medium text-gray-600">Current Balance</p>
          <p class="text-2xl font-bold text-gray-900">{{ formatNaira($page.props.dashboardStats.current_balance) }}</p>
          <!-- <p class="text-xs text-gray-500">Available funds</p>  -->
        </div>
      </div>

      <!-- Initial Contribution -->
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
            </svg>
          </div>
          <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Initial</span>
        </div>
        <div class="space-y-1">
          <p class="text-sm font-medium text-gray-600">Initial Contribution</p>
          <p class="text-2xl font-bold text-gray-900">{{ formatNaira($page.props.dashboardStats.total_contribution) }}</p>
          <p class="text-xs text-gray-500">Units: {{ $page.props.dashboardStats.total_units }}</p>
        </div>
      </div>

      <!-- Investment Income -->
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <div class="p-2 bg-purple-100 rounded-lg">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
          </div>
          <span class="text-xs text-purple-600 bg-purple-100 px-2 py-1 rounded-full font-medium">+invest</span>
        </div>
        <div class="space-y-1">
          <p class="text-sm font-medium text-gray-600">Investment Income</p>
          <p class="text-2xl font-bold text-gray-900">{{ formatNaira($page.props.dashboardStats.investment_income) }}</p>
          <!-- <p class="text-xs text-gray-500"></p> -->
        </div>
      </div>

      <!-- Recent Contribution -->
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <div class="p-2 bg-orange-100 rounded-lg">
            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
            {{ formatTimeAgo($page.props.dashboardStats.fund_date) }}
          </span>
        </div>
        <div class="space-y-1">
          <p class="text-sm font-medium text-gray-600">Total Fund Price</p>
          <p class="text-2xl font-bold text-gray-900">{{ formatNaira($page.props.dashboardStats.fund_price) }}</p>
          <p class="text-xs text-gray-500">{{ formatLongDate($page.props.dashboardStats.fund_date) }}</p>
        </div>
      </div>
    </div>

    <!-- Simplified Account Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Account Status -->
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center mb-4">
          <div class="p-2 bg-lime-100 rounded-lg mr-3">
            <svg class="w-6 h-6 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0a4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900">Account Information</h3>
        </div>
        
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Account Status</span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-lime-100 text-lime-800">
              Active
            </span>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Name</span>
            <span class="text-sm font-medium text-gray-900">{{ $page.props.auth.user.name }}</span>
          </div>

            <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Username</span>
            <span class="text-sm font-medium text-gray-900">{{ $page.props.auth.user.user_name }}</span>
          </div>

          
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">PIN</span>
            <span class="text-sm font-medium text-gray-900">{{ $page.props.auth.user.pin }}</span>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Branch</span>
            <span class="text-sm font-medium text-gray-900">{{ $page.props.auth.user.branch_description || 'N/A' }}</span>
          </div>
          
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center mb-4">
          <div class="p-2 bg-blue-100 rounded-lg mr-3">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
        </div>
        
        <div class="space-y-4">
          <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 w-2 h-2 bg-lime-500 rounded-full mt-2"></div>
            <div>
              <p class="text-sm font-medium text-gray-900">Using Default Password</p>
              <p class="text-xs text-gray-500">{{ $page.props.auth.user.is_default_password ? 'Yes, change password' : 'No' }}</p>
            </div>
          </div>
          
          <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
            <div>
              <p class="text-sm font-medium text-gray-900">Last Updated Password At</p>
              <p class="text-xs text-gray-500">{{ $page.props.dashboardStats.last_password_update }}</p>
            </div>
          </div>
          
          <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 w-2 h-2 bg-orange-500 rounded-full mt-2"></div>
            <div>
              <p class="text-sm font-medium text-gray-900">Password will Expire AT</p>
              <p class="text-xs text-gray-500">{{ $page.props.dashboardStats.password_expiring_date }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
  import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
  import { Head, usePage } from "@inertiajs/vue3";

  const page = usePage();

  // Function to format Naira currency with commas and 2 decimal places
  const formatNaira = (amount) => {
    // Convert to number if it's a string
    const numAmount = typeof amount === 'string' ? parseFloat(amount) : amount;
    
    // Check if it's a valid number
    if (isNaN(numAmount)) return '₦0.00';
    
    // Round to 2 decimal places and format with commas
    return '₦' + numAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    
  };

  // Function to format number with commas and 2 decimal places (without currency symbol)
  const formatNumber = (amount) => {
    // Convert to number if it's a string
    const numAmount = typeof amount === 'string' ? parseFloat(amount) : amount;
    
    // Check if it's a valid number
    if (isNaN(numAmount)) return '0.00';
    
    // Round to 2 decimal places and format with commas
    return numAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  };

  // Function to format date as "August 15, 2024"
  const formatLongDate = (date) => {
    // Convert string to Date object if needed
    const dateObj = typeof date === 'string' ? new Date(date) : date;
    
    // Check if it's a valid date
    if (isNaN(dateObj.getTime())) return 'Invalid Date';
    
    // Format options for long date
    const options = { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    };
    
    return dateObj.toLocaleDateString('en-US', options);
  };

  // Function to format relative time like "41 days ago", "2 hours ago", etc.
  const formatTimeAgo = (date) => {
    // Convert string to Date object if needed
    const dateObj = typeof date === 'string' ? new Date(date) : date;
    
    // Check if it's a valid date
    if (isNaN(dateObj.getTime())) return 'Invalid Date';
    
    const now = new Date();
    const diffInMs = now - dateObj;
    const diffInSeconds = Math.floor(diffInMs / 1000);
    const diffInMinutes = Math.floor(diffInSeconds / 60);
    const diffInHours = Math.floor(diffInMinutes / 60);
    const diffInDays = Math.floor(diffInHours / 24);
    const diffInWeeks = Math.floor(diffInDays / 7);
    const diffInMonths = Math.floor(diffInDays / 30);
    const diffInYears = Math.floor(diffInDays / 365);
    
    if (diffInYears > 0) {
      return diffInYears === 1 ? '1 year ago' : `${diffInYears} years ago`;
    } else if (diffInMonths > 0) {
      return diffInMonths === 1 ? '1 month ago' : `${diffInMonths} months ago`;
    } else if (diffInWeeks > 0) {
      return diffInWeeks === 1 ? '1 week ago' : `${diffInWeeks} weeks ago`;
    } else if (diffInDays > 0) {
      return diffInDays === 1 ? '1 day ago' : `${diffInDays} days ago`;
    } else if (diffInHours > 0) {
      return diffInHours === 1 ? '1 hour ago' : `${diffInHours} hours ago`;
    } else if (diffInMinutes > 0) {
      return diffInMinutes === 1 ? '1 minute ago' : `${diffInMinutes} minutes ago`;
    } else {
      return 'Just now';
    }
  };

// Example usage:
// formatNaira(7154669.232435861) returns "₦7,154,669.23"
// formatNaira("7154669.232435861") returns "₦7,154,669.23"
// formatNaira(1000) returns "₦1,000.00"
// formatNumber(7154669.232435861) returns "7,154,669.23"
// formatNumber("7154669.232435861") returns "7,154,669.23"
// formatNumber(1000) returns "1,000.00"
// formatLongDate(new Date()) returns "September 26, 2025"
// formatLongDate("2024-08-15") returns "August 15, 2024"
// formatLongDate("2024-08-15T10:30:00Z") returns "August 15, 2024"
// formatTimeAgo(new Date(Date.now() - 41 * 24 * 60 * 60 * 1000)) returns "41 days ago"
// formatTimeAgo("2024-08-15") returns relative time from that date
// formatTimeAgo(new Date(Date.now() - 2 * 60 * 60 * 1000)) returns "2 hours ago"
</script>