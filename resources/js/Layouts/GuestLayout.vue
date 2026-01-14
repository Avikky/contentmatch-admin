<script setup>
import { computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const year = computed(() => new Date().getFullYear());
</script>

<template>
  <div class="min-h-screen cmBgDark flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-md backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl px-8 py-10 shadow-2xl">
      <div class="flex flex-col items-center gap-3 mb-10 text-center">
        <ApplicationLogo class="h-12 w-auto text-white" />
        <h1 class="text-2xl font-semibold cmWhite">ContentMatch Admin</h1>
        <p class="text-sm text-white/70">Secure access for portal administrators</p>
      </div>

      <slot />

      <p class="mt-10 text-center text-xs text-white/60">© {{ year }} ContentMatch. All rights reserved.</p>
    </div>
  </div>
</template>

<style scoped>
.cmBgDark {
  background-color: #0F051D !important;
  color: #FDFDFD;
}

.cmBgWhite {
  background-color: #f4f4f4 !important;
  color: #0F051D !important;
}

.cmBgWhite:hover {
  background-color: #e8e8e8 !important;
}

.cmWhite {
  color: #FDFDFD !important;
}

.cmWhiteHover:hover {
  color: #FFFFFF !important;
}
</style>
