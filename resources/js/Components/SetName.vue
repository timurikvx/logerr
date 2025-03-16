<template>
    <Modal :title="title" v-model:visible="modal.setName" class="set-name">
        <div class="flex">
            <input type="text" class="input grow mr-2" v-model="name">
            <button class="button" @click="complete">Установить</button>
        </div>
    </Modal>
</template>

<script setup>

    import Modal from "@/Components/Modal.vue";
    import {defineProps, defineEmits, onMounted, ref} from 'vue';
    import {modalStore} from "@/Store/Modal.js";

    const modal = modalStore();

    const props = defineProps({
        title: {
            type: String,
            default: 'Ввод наименования'
        },
        name: {
            type: String,
            default: ''
        }
    });

    const emits = defineEmits(['complete']);

    onMounted(()=>{
        name.value = props.name;
    });

    let name = ref('');

    function complete(){
        if(name.value.length === 0){
            return;
        }
        modal.setName = false;
        emits('complete', name.value);
    }

</script>

<style scoped>

</style>
