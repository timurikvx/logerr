<script setup>

    import { defineProps } from 'vue'
    import { router } from '@inertiajs/vue3';

    defineProps({
        title: {
            type: String,
            default: 'LogErr'
        }
    });

    const logout = () => {
        router.post('/logout');
    };

</script>

<template>
    <div class="header">
        <div class="flex">
            <div class="font-bold py-4">{{ title }}</div>
            <div class="grow self-center">
                <slot></slot>
            </div>
            <form method="POST" @submit.prevent="logout" class="self-center">
                <button type="submit">Выйти</button>
            </form>
        </div>
    </div>
</template>

<style scoped>

</style>
