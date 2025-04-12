<template>
    <Modal class="question" :title="question.title" v-model:visible="visible">
        <div class="p-2 mb-4">{{ question.question }}</div>
        <div class="p-2 flex">
            <button class="button red w-1/3 text-center uppercase font-bold" @click="close">
                <span class="m-auto">Нет</span>
            </button>
            <div class="grow"></div>
            <button class="button green w-1/3 text-center uppercase font-bold" @click="confirm">
                <span class="m-auto">Да</span>
            </button>
        </div>
    </Modal>
</template>

<script setup>

    import Modal from "@/Components/Modal.vue";
    import {defineProps, defineEmits, computed} from 'vue';

    const props = defineProps({
        visible: Boolean,
        question: Object
    })
    const emits = defineEmits(['update:visible', 'confirm']);

    let visible = computed({
        get(){
            return props.visible;
        },
        set(value){
            emits('update:visible', value);
        }
    })

    function close(){
        visible.value = false;
    }

    function confirm(){
        visible.value = false;
        emits('confirm', props.question);
    }

</script>

<style scoped>

</style>
