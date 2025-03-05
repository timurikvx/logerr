<script setup>

    import {defineProps} from 'vue'

    defineProps({
        title: String,
        visible: Boolean
    });

    const emits = defineEmits(['update:visible', 'close']);

    function close(){
        emits('update:visible', false);
        emits('close');
    }

</script>

<template>
    <div class="modal" v-if="visible">
        <div class="flex flex-col">
            <div class="flex head p-2">
                <div class="grow self-center">{{ title }}</div>
                <button class="close" @click="close"></button>
            </div>
            <div class="content p-2 grow flex flex-col">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
