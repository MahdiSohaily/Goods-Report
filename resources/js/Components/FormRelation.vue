<script setup>
import { computed, useSlots } from 'vue';

defineEmits(['submitted']);

const hasActions = computed(() => !!useSlots().actions);
</script>

<template>
    <div class="">
        <form @submit.prevent="$emit('submitted')">
            <div class=""
                :class="hasActions ? 'sm:rounded-tl-md sm:rounded-tr-md' : 'sm:rounded-md'">
                <div class="">
                    <slot name="form" />
                </div>
            </div>

            <div v-if="hasActions"
                class="">
                <slot name="actions" />
            </div>
        </form>
    </div>
</template>
