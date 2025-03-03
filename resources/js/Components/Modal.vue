<script setup>

    import {defineProps} from 'vue'

    defineProps({
        title: String
    })

    import { modalStore } from '@/Store/Modal.js'
    const store = modalStore();

    function close(){
        store.visible = false;
    }

</script>

<template>
    <div class="modal" v-if="store.visible">
        <div class="flex head p-2">
            <div class="grow self-center">{{ title }}</div>
            <button class="close" @click="close">X</button>
        </div>
        <div class="content p-2 grow flex flex-col">
            <slot></slot>
        </div>
    </div>
</template>

<style scoped>

</style>
