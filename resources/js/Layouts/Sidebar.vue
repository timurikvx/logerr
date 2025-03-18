<template>
    <div class="sidebar flex flex-col">
        <a v-if="crew?.name" class="crew p-3" target="_blank" :href="'/teams/' + crew.guid" :title="crew.name">
            <div class="mb-2 text-xs">Текущая команда:</div>
            <div class="font-bold text-xl truncate">{{ crew.name }}</div>
        </a>
        <a class="p-3 menu-item flex" :class="{active: url === '/dashboard'}" href="/dashboard">
            <div class="self-center">Панель управления</div>
        </a>
        <a class="p-3 menu-item flex" :class="{active: url.startsWith('/teams')}" href="/teams">
            <div class="self-center">Управление командами</div>
        </a>
        <a class="p-3 menu-item flex" :class="{active: url.startsWith('/errors')}" href="/errors">
            <div class="self-center">Просмотр ошибок</div>
        </a>
        <a class="p-3 menu-item flex" :class="{active: url.startsWith('/logs')}" href="/logs">
            <div class="self-center">Просмотр логов</div>
        </a>
        <div class="grow"></div>
    </div>
</template>

<script setup>

    import {modalStore} from "@/Store/Modal.js";
    import {defineProps} from "vue";

    const modal = modalStore();
    const url = location.pathname;

    defineProps({
        crew: {
            type: Object,
            default: {}
        }
    });

</script>

<style scoped>

</style>
