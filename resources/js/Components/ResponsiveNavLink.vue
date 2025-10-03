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
    ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-[#6C4FE0] text-start text-base font-medium text-[#0F051D] bg-[#F2EDFF] focus:outline-none focus:text-[#0F051D] focus:bg-[#E7DEFF] focus:border-[#0F051D] transition duration-150 ease-in-out'
    : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-slate-600 hover:text-[#0F051D] hover:bg-[#F7F4FF] hover:border-[#E0D8FF] focus:outline-none focus:text-[#0F051D] focus:bg-[#F7F4FF] focus:border-[#E0D8FF] transition duration-150 ease-in-out'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
