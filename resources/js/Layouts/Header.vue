<template>
    <div class="header">
        <div class="flex">
            <a class="flex" href="/dashboard">
                <div class="logo self-center mr-1"></div>
                <div class="font-bold py-4">{{ title }}</div>
            </a>
            <div class="grow self-center">
                <slot></slot>
            </div>
            <div v-if="$page.props.auth.user" class="flex">
                <div class="flex mr-4">
                    <button @click="show" class="self-center square-button notification relative">
                        <span v-if="notifications.exist" class="exist"></span>
                    </button>
                </div>
                <form method="POST" @submit.prevent="logout" class="self-center">
                    <button type="submit">Выйти</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>

    import { defineProps } from 'vue'
    import { router } from '@inertiajs/vue3';
    import {modalStore} from '@/Store/Modal.js';
    import {notificationsStore} from "@/Store/Notifications.js";

    const modal = modalStore();
    const notifications = notificationsStore();

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
