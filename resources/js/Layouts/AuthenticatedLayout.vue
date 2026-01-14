<script setup>
import { computed, ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ToastContainer from '@/Components/ToastContainer.vue';

const page = usePage();
const showMobile = ref(false);

const admin = computed(() => page.props.auth?.user ?? null);

const isSuperAdmin = computed(() => {
  return admin.value?.role === 'superadmin';
});

const navigation = computed(() => {
  const baseNavigation = [
    {
      label: 'Dashboard',
      name: 'admin.dashboard',
      icon: 'M4 6h16M4 12h16M4 18h16',
    },
    {
      label: 'Users',
      name: 'admin.users.index',
      icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    },
    {
      label: 'Communities',
      name: 'admin.communities.index',
      icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    },
    {
      label: 'Content',
      name: 'admin.content.index',
      icon: 'M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z',
    },
  ];

  // Add Admins menu item for superadmin only
  if (isSuperAdmin.value) {
    baseNavigation.push({
      label: 'Admins',
      name: 'admin.admins.index',
      icon: 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
    });
  }

  baseNavigation.push({
    label: 'Profile',
    name: 'admin.profile',
    icon: 'M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 0114 0H5z',
  });

  return baseNavigation;
});

const isActive = (name) => route().current(name);

const initials = computed(() => {
  if (!admin.value) return 'CM';
  const source =
    admin.value.display_name ||
    admin.value.full_name ||
    admin.value.name ||
    admin.value.email ||
    'CM';

  return source
    .split(' ')
    .filter(Boolean)
    .map((part) => part[0])
    .join('')
    .substring(0, 2)
    .toUpperCase();
});

const toggleMobile = () => {
  showMobile.value = !showMobile.value;
};

watch(
  () => page.url,
  () => {
    showMobile.value = false;
  }
);

// Note: Flash messages are now handled globally in app.js
// No need to watch for them here anymore
</script>

<template>
  <div class="min-h-screen flex bg-slate-900 text-white overflow-x-hidden">
    <transition name="fade">
      <div v-if="showMobile" class="fixed inset-0 z-30 bg-black/70 lg:hidden" @click="showMobile = false" />
    </transition>

    <transition name="slide">
      <aside v-if="showMobile" class="fixed inset-y-0 left-0 z-40 w-64 sm:w-72 cmBgDark lg:hidden flex flex-col overflow-hidden">
        <div class="px-4 sm:px-6 py-4 sm:py-6 border-b border-white/10 flex items-center gap-3">
          <ApplicationLogo class="h-8 sm:h-9 w-auto text-white flex-shrink-0" />
          <div class="min-w-0">
            <p class="text-sm font-semibold cmWhite truncate">ContentMatch Admin</p>
            <p class="text-xs text-white/60 truncate">Control Center</p>
          </div>
        </div>
        <nav class="flex-1 px-3 sm:px-4 py-4 sm:py-6 space-y-1 overflow-y-auto">
          <Link
            v-for="item in navigation"
            :key="item.name"
            :href="route(item.name)"
            class="flex items-center gap-3 rounded-xl px-3 sm:px-4 py-3 text-sm font-medium transition"
            :class="isActive(item.name)
              ? 'bg-white/20 cmWhite shadow-inner'
              : 'text-white/60 hover:bg-white/10 cmWhiteHover'"
          >
            <span class="inline-flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-lg bg-white/10 flex-shrink-0">
              <svg class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" />
              </svg>
            </span>
            <span class="truncate">{{ item.label }}</span>
          </Link>
        </nav>
      </aside>
    </transition>

    <aside class="hidden lg:flex lg:w-64 xl:w-72 cmBgDark flex-col border-r border-white/10 flex-shrink-0">
      <div class="px-6 xl:px-8 py-6 xl:py-8 border-b border-white/10 flex flex-col gap-4">
        <ApplicationLogo class="h-10 w-auto text-white" />
        <div>
          <p class="text-base xl:text-lg font-semibold cmWhite">ContentMatch Admin</p>
          <p class="text-xs text-white/60">Operations Console</p>
        </div>
      </div>
      <nav class="flex-1 px-4 xl:px-5 py-6 xl:py-8 space-y-1 overflow-y-auto">
        <Link
          v-for="item in navigation"
          :key="item.name"
          :href="route(item.name)"
          class="flex items-center gap-3 rounded-xl px-3 xl:px-4 py-3 text-sm font-medium transition"
          :class="isActive(item.name)
            ? 'bg-white/20 cmWhite shadow-inner'
            : 'text-white/60 hover:bg-white/10 cmWhiteHover'"
        >
          <span class="inline-flex h-8 w-8 xl:h-9 xl:w-9 items-center justify-center rounded-lg bg-white/10 flex-shrink-0">
            <svg class="h-4 w-4 xl:h-5 xl:w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" />
            </svg>
          </span>
          <span>{{ item.label }}</span>
        </Link>
      </nav>
      <div class="px-4 xl:px-6 pb-6 xl:pb-8">
        <div class="rounded-2xl border border-white/10 bg-white/10 p-4 xl:p-5">
          <p class="text-sm font-semibold cmWhite">Need a hand?</p>
          <p class="mt-2 text-xs text-white/70">
            Reach out to the ContentMatch success team for quick assistance and product guidance.
          </p>
          <a href="mailto:support@contentmatch.test" class="mt-4 inline-flex items-center justify-center rounded-lg bg-white px-3 py-2 text-xs font-semibold text-slate-900 w-full">Contact support</a>
        </div>
      </div>
    </aside>

    <div class="flex-1 flex flex-col bg-slate-50 text-slate-900 min-w-0 overflow-x-hidden">
      <header class="sticky top-0 z-20 bg-white/80 backdrop-blur border-b border-slate-200">
        <div class="h-16 sm:h-20 px-3 sm:px-4 lg:px-6 xl:px-10 flex items-center justify-between gap-2 sm:gap-4">
          <div class="flex items-center gap-2 sm:gap-3 min-w-0">
            <button
              type="button"
              class="inline-flex lg:hidden items-center justify-center rounded-lg border border-slate-200 bg-white px-2 sm:px-3 py-2 text-slate-700 shadow-sm flex-shrink-0"
              @click="toggleMobile"
            >
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
            <div class="hidden sm:block min-w-0">
              <p class="text-xs uppercase tracking-wide text-slate-400 truncate">Environment</p>
              <p class="text-sm font-semibold text-slate-800 truncate">Admin Portal</p>
            </div>
          </div>
          <div class="flex items-center gap-2 sm:gap-3 lg:gap-4">
            <div class="hidden md:flex flex-col items-end min-w-0">
              <span class="text-sm font-semibold text-slate-800 truncate">{{ admin?.display_name ?? admin?.name ?? 'Administrator' }}</span>
              <span class="text-xs text-slate-500 truncate">{{ admin?.role ?? 'admin' }}</span>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
              <div class="flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-full cmBgDark text-xs sm:text-sm font-semibold flex-shrink-0">
                {{ initials }}
              </div>
              <Link
                :href="route('admin.logout')"
                method="post"
                as="button"
                class="hidden sm:inline-flex items-center rounded-full border border-slate-200 bg-white px-3 lg:px-4 py-1.5 lg:py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100"
              >
                Sign out
              </Link>
              <Link
                :href="route('admin.logout')"
                method="post"
                as="button"
                class="sm:hidden inline-flex items-center justify-center rounded-full border border-slate-200 bg-white p-2 text-slate-700 hover:bg-slate-100">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
              </Link>
            </div>
          </div>
        </div>
      </header>

      <main class="flex-1 px-3 sm:px-4 lg:px-6 xl:px-10 py-4 sm:py-6 lg:py-8 overflow-x-hidden">
        <slot />
      </main>
    </div>

    <!-- Global Toast Notifications -->
    <ToastContainer />
  </div>
</template>

<style scoped>
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
  transition: transform 0.25s ease, opacity 0.25s ease;
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
