<template>
    <button class="button">
        <span v-if="icon" class="button-icon" :class="icon"></span>
        <span>
            <slot></slot>
        </span>
    </button>
</template>

<script setup>

    import {defineProps} from 'vue';

    defineProps({
        icon: {
            type: String,
            default: ''
        }
    })

</script>

<style scoped>

</style>
