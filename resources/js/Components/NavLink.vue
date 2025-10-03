<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
    ? 'inline-flex items-center px-1 pt-1 border-b-2 border-[#6C4FE0] text-sm font-medium leading-5 text-[#0F051D] focus:outline-none focus:border-[#0F051D] transition duration-150 ease-in-out'
    : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-slate-500 hover:text-[#0F051D] hover:border-[#E0D8FF] focus:outline-none focus:text-[#0F051D] focus:border-[#E0D8FF] transition duration-150 ease-in-out'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
