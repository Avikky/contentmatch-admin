<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
</script>

<style>
/* Portal Title Styles */
.portal-title {
  background: linear-gradient(135deg, #09518c 0%, #8B4513 100%);
  background-size: 200% 200%;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  opacity: 0.12;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  letter-spacing: 0.05em;
  text-shadow: 0 2px 4px rgba(9, 81, 140, 0.08);
  animation: gradient-shift 8s ease-in-out infinite;
  transform: translateZ(0); /* Hardware acceleration */
  will-change: background-position;
}

/* Mobile responsive adjustments for portal title */
@media (max-width: 640px) {
  .portal-title {
    opacity: 0.08;
    letter-spacing: 0.02em;
    line-height: 1.1;
  }
  
  .portal-title-top {
    display: none;
  }
  
  .portal-title-bottom {
    display: block;
  }
}

@media (min-width: 641px) and (max-width: 768px) {
  .portal-title {
    opacity: 0.08;
    letter-spacing: 0.03em;
  }
  
  .portal-title-top {
    display: block;
  }
  
  .portal-title-bottom {
    display: none;
  }
}

@media (min-width: 769px) {
  .portal-title {
    opacity: 0.12;
  }
  
  .portal-title-top {
    display: block;
  }
  
  .portal-title-bottom {
    display: none;
  }
}

@keyframes gradient-shift {
  0%, 100% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
}

/* Prevent horizontal scrolling on small screens */
@media (max-width: 375px) {
  .portal-title {
    font-size: 1.125rem; /* 18px */
    line-height: 1.2;
  }
  
  .portal-title span:last-child {
    font-size: 1rem; /* 16px */
  }
}
</style>

<template>
  <div class="min-h-screen md:bg-[#F8F9FC] relative">
    <!-- Portal Title - Top (Hidden on mobile) -->
    <div class="portal-title-top absolute top-2 sm:top-4 md:top-8 left-0 right-0 z-10 px-2 sm:px-4">
      <h1 class="portal-title text-xl sm:text-2xl md:text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl font-black text-center leading-tight tracking-wider select-none">
        <span class="block sm:block">ContentMatch Admin</span>
        <br class="hidden xs:block sm:hidden">
        <span class="text-lg sm:text-xl md:text-3xl lg:text-4xl xl:text-5xl 2xl:text-6xl block sm:block md:inline">PORTAL</span>
      </h1>
    </div>

    <!-- Login Card Container -->
    <div class="min-h-screen flex flex-col sm:justify-center items-center lg:pt-6 sm:pt-0 px-4 sm:px-0">
      <div class="w-full sm:max-w-md mx-auto mt-6 px-4 sm:px-6 py-4 bg-[#F8F9FC] md:bg-white shadow-md sm:rounded-lg relative z-20">
        
        <!-- Logo -->
        <div class="flex justify-center mb-4 sm:mb-6">
          <Link href="/">
            <ApplicationLogo class="w-36 sm:w-40 md:w-48 fill-current text-gray-500" />
          </Link>
        </div>

        <!-- Dynamic Content Slot -->
        <slot />

        <!-- Copyright -->
        <p class="text-center text-[#3F444D66]/40 text-xs sm:text-sm mt-4 sm:mt-6">
          {{ new Date().getFullYear() }} &copy;. All rights reserved.
        </p>
      </div>
    </div>

    <!-- Portal Title - Bottom (Shown only on mobile) -->
    <div class="portal-title-bottom hidden px-4 mt-6 mb-8 absolute bottom-20 left-0 right-0">
      <h1 class="portal-title text-2xl font-black text-center leading-tight tracking-wider select-none">
        <span class="block">ContentMatch Admin</span>
        <span class="text-xl block">PORTAL</span>
      </h1>
    </div>

    <!-- Background Illustrations -->
 
  </div>
</template>
