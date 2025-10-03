<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 bg-white border border-[#0F051D]\/20 rounded-lg font-semibold text-xs uppercase tracking-widest text-[#0F051D] shadow-sm hover:bg-[#F4F1FF] focus:outline-none focus:ring-2 focus:ring-[#6C4FE0] focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
    >
        <slot />
    </button>
</template>
