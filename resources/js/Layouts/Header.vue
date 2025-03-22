<template>
    <div class="header">
        <div class="flex">
            <div class="font-bold py-4">{{ title }}</div>
            <div class="grow self-center">
                <slot></slot>
            </div>
            <div class="flex mr-4">
                <button @click="show" class="self-center square-button notification"></button>
            </div>
            <form method="POST" @submit.prevent="logout" class="self-center">
                <button type="submit">Выйти</button>
            </form>
        </div>
    </div>
</template>

<script setup>

    import { defineProps } from 'vue'
    import { router } from '@inertiajs/vue3';
    import {modalStore} from '@/Store/Modal.js';

    const modal = modalStore();
    const logout = () => {
        router.post('/logout');
    };

    defineProps({
        title: {
            type: String,
            default: 'LogErr'
        }
    });

    function show(){
        modal.notifications = true;
    }


</script>

<style scoped>

</style>
