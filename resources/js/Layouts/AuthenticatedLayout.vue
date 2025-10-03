<script setup>
import { computed, ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const page = usePage();
const showMobileSidebar = ref(false);

const user = computed(() => page.props.auth?.user ?? null);

const navigation = [
  {
    label: 'Dashboard',
    name: 'admin.dashboard',
    icon: 'M3 3h18a1 1 0 011 1v16a1 1 0 01-1 1H3a1 1 0 01-1-1V4a1 1 0 011-1zm1 4v12h16V7H4zm5 3h6a1 1 0 010 2H9a1 1 0 010-2z',
  },
  {
    label: 'Users',
    name: 'admin.users.index',
    icon: 'M17 20h5v-2a4 4 0 00-5-3.87M9 11a4 4 0 100-8 4 4 0 000 8zm0 2c-4 0-6 2-6 4v2h12v-2c0-2-2-4-6-4z',
  },
  {
    label: 'Communities',
    name: 'admin.communities.index',
    icon: 'M17 20a2 2 0 002-2v-5a2 2 0 00-.59-1.41l-6-6a2 2 0 00-2.82 0l-6 6A2 2 0 003 13v5a2 2 0 002 2h12z',
  },
  {
    label: 'Admins',
    name: 'admin.admins.index',
    icon: 'M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 0114 0H5z',
  },
  {
    label: 'Reports',
    name: 'admin.reports.index',
    icon: 'M4 19h16M7 10h4v9H7zm6-6h4v15h-4z',
  },
  {
    label: 'Profile',
    name: 'admin.profile.show',
    icon: 'M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 0114 0H5z',
  },
];

const heading = computed(() => {
  if (route().current('admin.dashboard')) {
    return 'Dashboard';
  }

  if (route().current('admin.users.*') || route().current('admin.users.index')) {
    return 'User Management';
  }

  if (route().current('admin.communities.*') || route().current('admin.communities.index')) {
    return 'Community Management';
  }

  if (route().current('admin.admins.*') || route().current('admin.admins.index')) {
    return 'Administrators';
  }

  if (route().current('admin.reports.*') || route().current('admin.reports.index')) {
    return 'Reports & Analytics';
  }

  if (route().current('admin.profile.*') || route().current('admin.profile.show')) {
    return 'Profile';
  }

  return page.props.title ?? 'Overview';
});

const resolveFlash = (value) => (typeof value === 'function' ? value() : value);

const flash = computed(() => {
  const raw = page.props.flash ?? {};
  return {
    success: resolveFlash(raw.success),
    error: resolveFlash(raw.error),
  };
});

const isRouteActive = (name) => {
  if (route().current(name)) {
    return true;
  }

  if (name.endsWith('.index')) {
    const base = name.replace('.index', '');
    return route().current(`${base}.*`);
  }

  return false;
};

const initials = computed(() => {
  if (!user.value) return 'CM';
  if (user.value.initials) return user.value.initials;

  const source = user.value.display_name || user.value.full_name || user.value.name || 'CM';
  return source
    .split(' ')
    .filter(Boolean)
    .map((part) => part[0])
    .join('')
    .substring(0, 2)
    .toUpperCase();
});

const toggleMobileSidebar = () => {
  showMobileSidebar.value = !showMobileSidebar.value;
};

watch(
  () => page.url,
  () => {
    showMobileSidebar.value = false;
  }
);
</script>

<template>
  <div class="min-h-screen bg-[#0F051D] flex">
    <!-- Mobile sidebar overlay -->
    <transition name="fade">
      <div
        v-if="showMobileSidebar"
        class="fixed inset-0 z-40 bg-black/40 lg:hidden"
        @click="showMobileSidebar = false"
      />
    </transition>

    <!-- Mobile sidebar -->
    <transition name="slide">
      <aside
        v-if="showMobileSidebar"
        class="fixed inset-y-0 left-0 z-50 w-72 bg-[#14092A] shadow-2xl lg:hidden"
      >
        <div class="flex h-16 items-center gap-3 px-6 border-b border-white/10">
          <ApplicationLogo class="h-8 w-auto" />
          <div class="flex flex-col">
            <span class="text-sm font-semibold text-white">ContentMatch Admin</span>
            <span class="text-xs text-white/60">Control Center</span>
          </div>
        </div>
        <nav class="px-4 py-6 space-y-1 overflow-y-auto">
          <Link
            v-for="item in navigation"
            :key="item.name"
            :href="route(item.name)"
            class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition"
            :class="isRouteActive(item.name)
              ? 'bg-white/10 text-white shadow-inner'
              : 'text-white/60 hover:bg-white/5 hover:text-white'"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="item.icon" />
            </svg>
            {{ item.label }}
          </Link>
        </nav>
      </aside>
    </transition>

    <!-- Desktop sidebar -->
    <aside class="hidden lg:flex lg:w-72 xl:w-80 bg-[#14092A] text-white flex-col border-r border-white/5">
      <div class="h-20 flex items-center gap-3 px-8 border-b border-white/10">
        <ApplicationLogo class="h-10 w-auto" />
        <div>
          <p class="text-base font-semibold">ContentMatch Admin</p>
          <p class="text-xs text-white/60">Operations Console</p>
        </div>
      </div>
      <nav class="flex-1 px-5 py-8 space-y-1 overflow-y-auto">
        <Link
          v-for="item in navigation"
          :key="item.name"
          :href="route(item.name)"
          class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition"
          :class="isRouteActive(item.name)
            ? 'bg-white/10 text-white shadow-inner'
            : 'text-white/60 hover:bg-white/5 hover:text-white'"
        >
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="item.icon" />
          </svg>
          <span>{{ item.label }}</span>
        </Link>
      </nav>
      <div class="px-6 pb-8">
        <div class="rounded-xl border border-white/10 bg-white/5 p-4">
          <p class="text-sm font-semibold text-white">Need insights?</p>
          <p class="mt-2 text-xs text-white/70">
            Visit the reports area to review community health, trending hashtags, and engagement signals.
          </p>
          <Link
            :href="route('admin.reports.index')"
            class="mt-4 inline-flex items-center justify-center rounded-lg bg-white/90 px-3 py-2 text-xs font-semibold text-[#14092A] hover:bg-white"
          >
            View analytics
          </Link>
        </div>
      </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col bg-[#F9F8FC]">
      <header class="sticky top-0 z-30 backdrop-blur bg-white/80 border-b border-slate-200">
        <div class="flex h-20 items-center justify-between px-4 sm:px-6 lg:px-10">
          <div class="flex items-center gap-3">
            <button
              type="button"
              class="inline-flex lg:hidden items-center justify-center rounded-lg border border-slate-200 bg-white p-2 text-slate-600 hover:bg-slate-100"
              @click="toggleMobileSidebar"
            >
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
            <div class="hidden lg:flex">
              <h1 class="text-sm font-semibold text-slate-700">{{ heading }}</h1>
            </div>
          </div>

          <div class="flex items-center gap-4">
            <div class="hidden md:flex items-center gap-2 rounded-full bg-white px-4 py-2 shadow-sm border border-slate-200">
              <span class="text-xs font-medium text-slate-500">Environment</span>
              <span class="text-xs font-semibold text-[#0F051D]">Admin</span>
            </div>

            <Dropdown align="right" width="56">
              <template #trigger>
                <button type="button" class="flex items-center gap-3 rounded-full border border-transparent bg-white px-2 py-1 shadow-sm hover:border-slate-200">
                  <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#0F051D] text-sm font-semibold text-white">
                    {{ initials }}
                  </div>
                  <div class="hidden sm:flex flex-col items-start">
                    <span class="text-sm font-semibold text-slate-700">{{ user?.display_name }}</span>
                    <span class="text-xs text-slate-500">{{ user?.email }}</span>
                  </div>
                  <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.08l3.71-3.85a.75.75 0 111.08 1.04l-4.25 4.41a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                  </svg>
                </button>
              </template>

              <template #content>
                <DropdownLink :href="route('admin.profile.show')">Profile settings</DropdownLink>
                <DropdownLink :href="route('admin.reports.index')">Analytics</DropdownLink>
                <DropdownLink :href="route('logout')" method="post" as="button">Sign out</DropdownLink>
              </template>
            </Dropdown>
          </div>
        </div>
      </header>

      <transition name="fade">
        <div v-if="flash.success || flash.error" class="px-4 sm:px-6 lg:px-10 mt-6">
          <div
            v-if="flash.success"
            class="mb-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
          >
            {{ flash.success }}
          </div>
          <div
            v-if="flash.error"
            class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"
          >
            {{ flash.error }}
          </div>
        </div>
      </transition>

      <main class="flex-1 px-4 pb-12 pt-6 sm:px-6 lg:px-10">
        <slot />
      </main>
    </div>
  </div>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
  transition: transform 0.2s ease, opacity 0.2s ease;
}

.slide-enter-from,
.slide-leave-to {
  transform: translateX(-100%);
  opacity: 0;
}

.cmBgDark {
  background-color: #0F051D !important;
  color: #FDFDFD;
}

.cmWhite {
  color: #FDFDFD !important;
}

.cmWhiteHover:hover {
  color: #FFFFFF !important;
}
</style>
