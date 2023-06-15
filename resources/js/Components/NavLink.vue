<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return props.active
        ? 'inline-flex items-center py-3 pr-6 text-sm font-medium leading-5 text-white bg-indigo-500 focus:outline-none  transition duration-150 ease-in-out'
        : 'inline-flex items-center py-3 pr-6 text-sm font-medium leading-5 text-gray-500 hover:bg-indigo-500 hover:text-white focus:outline-none transition duration-150 ease-in-out';
});
</script>

<template>
    <Link :href="href" :class="classes" >
        <slot />
    </Link>
</template>
