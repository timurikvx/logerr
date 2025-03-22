<template>
    <div class="modal-back" :class="back_class" v-if="visible"></div>
    <div class="modal flex flex-col overflow-hidden z-10" :class="getClass()" v-if="visible">
        <div class="flex flex-col my-auto">
            <div class="flex head p-2">
                <div class="grow self-center">{{ title }}</div>
                <button class="close" @click="close"></button>
            </div>
            <div class="content p-4 grow flex flex-col overflow-hidden">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script setup>

    import {defineProps} from 'vue'

    const props = defineProps({
        title: String,
        class: String,
        visible: Boolean,
        back_class: {
            type: String,
            default: ''
        }
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

<style scoped>

</style>
