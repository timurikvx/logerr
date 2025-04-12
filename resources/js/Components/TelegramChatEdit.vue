<template>
    <Modal :title="title" class="telegram-form" v-model:visible="modal.telegramChat" @close="close()">
        <form @submit.prevent="save" class="flex flex-col">
            <div class="flex flex-col mb-2">
                <div class="mb-1">Имя</div>
                <input type="text" class="input" required v-model="form.name">
            </div>
            <div class="flex flex-col mb-2">
                <div class="mb-1">Токен</div>
                <input type="text" class="input" required v-model="form.token">
            </div>
            <div class="flex flex-col mb-4">
                <div class="mb-1">Идентификатор чата</div>
                <input type="text" class="input" required v-model="form.chat_id">
            </div>
            <div class="flex">
                <div class="grow"></div>
                <button class="button" :disabled="form.processing">Создать</button>
            </div>
        </form>
    </Modal>
</template>

<script setup>

    import Modal from "@/Components/Modal.vue";
    import {modalStore} from "@/Store/Modal.js";
    import {defineProps, defineEmits, computed, defineExpose} from 'vue'
    import {useForm} from "@inertiajs/vue3";
    import axios from "axios";

    const modal = modalStore();
    const emits = defineEmits(['save']);
    const props = defineProps({
        //chat: Object,
        create: {
            type: Boolean,
            default: true
        }
    });

    defineExpose({
        update
    })

    let title = computed({
        get(){
            return props.create? 'Создание чата': 'Изменение чата';
        }
    });
    let form = useForm({
        name: '',
        token: '',
        chat_id: '',
        guid: ''
    });

    function save(){
        axios.post('/telegram/chat/save', form.data()).then(function (response){
            form.reset();
            modal.telegramChat = false;
            emits('save', response.data.list);
        });
    }

    function update(item){
        form.name = item.name;
        form.token = item.token;
        form.chat_id = item.chat_id;
        form.guid = item.guid;
    }

    function close(){
        form.reset();
    }

</script>

<style scoped>

</style>
