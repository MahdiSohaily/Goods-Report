<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Components/Welcome.vue";
defineProps({ rates: Object });
</script>

<template>
    <AppLayout title="Search">
        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <Welcome :rates="rates" />
            </div>
        </div>
    </AppLayout>
</template>
