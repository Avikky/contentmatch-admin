<template>
  <AuthenticatedLayout>
    <Head title="Dashboard" />

    <section class="space-y-4 sm:space-y-6">
      <div class="rounded-2xl sm:rounded-3xl bg-gradient-to-r from-[#14092A] to-[#2B0F4A] px-4 sm:px-6 lg:px-8 py-6 sm:py-8 lg:py-10 text-white shadow-xl">
        <p class="text-xs sm:text-sm uppercase tracking-wider sm:tracking-[0.3em] text-white/60">Welcome back</p>
        <h1 class="mt-3 sm:mt-4 text-2xl sm:text-3xl font-semibold cmWhite">
          {{ admin?.name || admin?.display_name || 'ContentMatch Admin' }}
        </h1>
        <p class="mt-2 sm:mt-3 max-w-xl text-xs sm:text-sm text-white/70">
          Monitor the health of your communities, review upcoming actions, and keep an eye on the signals that matter to ContentMatch.
        </p>
      </div>

      <div class="grid gap-3 sm:gap-4 lg:gap-5 grid-cols-1 sm:grid-cols-2 xl:grid-cols-3">
        <div
          v-for="metric in metrics"
          :key="metric.label"
          class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm ring-1 ring-slate-200/60"
        >
          <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ metric.label }}</p>
          <p class="mt-3 sm:mt-4 text-2xl sm:text-3xl font-semibold text-slate-900">{{ metric.value }}</p>
          <p class="mt-1 text-xs text-slate-400">Updated moments ago</p>
        </div>
      </div>

      <div class="grid gap-4 sm:gap-5 lg:gap-6 grid-cols-1 lg:grid-cols-2">
        <div class="rounded-xl sm:rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 lg:p-6 shadow-sm">
          <h2 class="text-base sm:text-lg font-semibold text-slate-900">Next steps</h2>
          <ul class="mt-3 sm:mt-4 space-y-3 sm:space-y-4 text-sm text-slate-600">
            <li class="flex items-start gap-2 sm:gap-3">
              <span class="mt-1 h-2 w-2 sm:h-2.5 sm:w-2.5 rounded-full bg-emerald-400 flex-shrink-0"></span>
              <div class="min-w-0">
                <p class="font-medium text-slate-800 text-xs sm:text-sm">Review scheduled community launches</p>
                <p class="text-xs text-slate-500 mt-0.5">Ensure each launch plan has an assigned owner.</p>
              </div>
            </li>
            <li class="flex items-start gap-2 sm:gap-3">
              <span class="mt-1 h-2 w-2 sm:h-2.5 sm:w-2.5 rounded-full bg-sky-400 flex-shrink-0"></span>
              <div class="min-w-0">
                <p class="font-medium text-slate-800 text-xs sm:text-sm">Audit manager activity</p>
                <p class="text-xs text-slate-500 mt-0.5">Verify that approvals from this week have been processed.</p>
              </div>
            </li>
            <li class="flex items-start gap-2 sm:gap-3">
              <span class="mt-1 h-2 w-2 sm:h-2.5 sm:w-2.5 rounded-full bg-amber-400 flex-shrink-0"></span>
              <div class="min-w-0">
                <p class="font-medium text-slate-800 text-xs sm:text-sm">Update knowledge base</p>
                <p class="text-xs text-slate-500 mt-0.5">Share new moderation guidelines with the wider team.</p>
              </div>
            </li>
          </ul>
        </div>

        <div class="rounded-xl sm:rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 lg:p-6 shadow-sm">
          <h2 class="text-base sm:text-lg font-semibold text-slate-900">Announcements</h2>
          <div class="mt-3 sm:mt-4 space-y-3 sm:space-y-4 text-sm text-slate-600">
            <p class="rounded-xl sm:rounded-2xl bg-slate-100 px-3 sm:px-4 py-2.5 sm:py-3 text-slate-700 text-xs sm:text-sm">
              <strong class="text-slate-900">Platform refresh.</strong> The analytics module receives a new look next week.
            </p>
            <p class="rounded-xl sm:rounded-2xl bg-slate-100 px-3 sm:px-4 py-2.5 sm:py-3 text-slate-700 text-xs sm:text-sm">
              <strong class="text-slate-900">Security audit.</strong> Rotate manager passwords every 60 days to stay compliant.
            </p>
            <p class="rounded-xl sm:rounded-2xl bg-slate-100 px-3 sm:px-4 py-2.5 sm:py-3 text-slate-700 text-xs sm:text-sm">
              <strong class="text-slate-900">Community spotlight.</strong> "Creators United" surpassed 10k members this month.
            </p>
          </div>
        </div>
      </div>
    </section>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const page = usePage();

const admin = computed(() => page.props.admin ?? page.props.auth?.user ?? {});
const metrics = computed(() => page.props.metrics ?? []);
</script>
