<template>
    <Modal title="Копирование чата из другой команды" class="telegram-form-copy" v-model:visible="modal.telegramChatCopy">
        <div class="mb-2">Список чатов других команд</div>
        <div class="flex flex-col grow overflow-hidden mb-4">
            <div v-for="item in chats" class="flex p-2">
                <input type="checkbox" class="self-center mr-4" v-model="item.select">
                <div class="grow mr-4">{{ item.name }}</div>
                <div>{{ item.team?.name }}</div>
            </div>
        </div>
        <div class="flex">
            <div class="grow"></div>
            <button class="button" @click="copy">Скопировать выбранное</button>
        </div>
    </Modal>
</template>

<script setup>

    import Modal from "@/Components/Modal.vue";
    import {modalStore} from "@/Store/Modal.js";
    import {ref, defineExpose, defineEmits} from 'vue'
    import axios from "axios";

    const modal = modalStore();
    const emits = defineEmits('copied');

    defineExpose({
        update
    })

    let chats = ref([]);

    function update(){
        axios.post('/telegram/chat/teams/get').then(function (response){
            chats.value = response.data.chats;
        })
    }

    function copy(){
        let data = {
            'chats': chats.value.filter((item)=> item.select)
        }
        axios.post('/telegram/chat/teams/copy', data).then(function (response){
            modal.telegramChatCopy = false;
            emits('copied', response.data.list);
        });
    }


</script>

<style scoped>

</style>
