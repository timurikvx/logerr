<script setup>

    import {defineProps} from 'vue'

    const props = defineProps({
        title: String,
        class: String,
        visible: Boolean
    });
    const emits = defineEmits(['update:visible', 'close']);

    function close(){
        emits('update:visible', false);
        emits('close');
    }

    function getClass(){
        return props.class;
    }

</script>

<template>
    <div class="modal-back" v-if="visible"></div>
    <div class="modal flex flex-col overflow-hidden z-10" :class="getClass()" v-if="visible">
        <div class="flex flex-col my-auto">
            <div class="flex head p-2">
                <div class="grow self-center">{{ title }}</div>
                <button class="close" @click="close"></button>
            </div>
            <div class="content m-4 grow flex flex-col overflow-hidden">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
