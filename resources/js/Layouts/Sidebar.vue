<template>
    <div class="sidebar flex flex-col" :class="{short: short}">
        <a v-if="crew?.name" class="crew p-3" target="_blank" :href="'/teams/' + crew.guid" :title="crew.name">
            <div v-if="!short" class="mb-2 text-xs">Текущая команда:</div>
            <div v-if="!short" class="font-bold text-xl truncate">{{ crew.name }}</div>
        </a>
        <a class="p-3 menu-item flex" :class="{active: url === '/dashboard'}" href="/dashboard">
            <div class="dashboard-icon icon" title="Панель управления"></div>
            <div v-if="!short" class="self-center ml-4">Панель управления</div>
        </a>
        <a class="p-3 menu-item flex" :class="{active: url.startsWith('/teams')}" href="/teams">
            <div class="teams-icon icon" title="Управление командами"></div>
            <div v-if="!short" class="self-center ml-4">Управление командами</div>
        </a>
        <a class="p-3 menu-item flex" :class="{active: url.startsWith('/errors')}" href="/errors">
            <div class="err-icon icon" title="Просмотр ошибок"></div>
            <div v-if="!short" class="self-center ml-4">Просмотр ошибок</div>
        </a>
        <a class="p-3 menu-item flex" :class="{active: url.startsWith('/logs')}" href="/logs">
            <div class="log-icon icon" title="Просмотр логов"></div>
            <div v-if="!short" class="self-center ml-4">Просмотр логов</div>
        </a>
        <a class="p-3 menu-item flex" :class="{active: url.startsWith('/notifications')}" href="/notifications">
            <div class="notifications-icon icon" title="Оповещения"></div>
            <div v-if="!short" class="self-center ml-4">Оповещения</div>
        </a>
        <div class="grow"></div>
        <div class="icon m-3" :class="{'expand-panel': short, 'collapse-panel': !short}" title="Свернуть/развернуть" @click="changeShort()"></div>
    </div>
</template>

<script setup>

    import {modalStore} from "@/Store/Modal.js";
    import {ref, defineProps, onMounted, inject} from "vue";
    import axios from "axios";

    const modal = modalStore();
    const url = location.pathname;
    const props = defineProps({
        crew: {
            type: Object,
            default: {}
        }
    });

    onMounted(()=>{
        short.value = inject('short', false);
    });

    let short = ref(false);

    function changeShort(){
        short.value = !short.value;
        axios.post('/option/set', {name: 'short', value: short.value});
    }

</script>

<style scoped>

</style>
