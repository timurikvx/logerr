<template>
    <Modal title="Копирование чата из другой команды" class="telegram-form-copy" v-model:visible="modal.telegramChatCopy">
        <div class="mb-2">Список чатов других команд</div>
        <div class="flex flex-col grow overflow-hidden mb-4">
            <div v-for="item in chats" class="flex p-2">
                <input type="checkbox" class="self-center mr-4" v-model="item.select">
                <div class="grow">{{ item.name }}</div>
                <div class="mr-4">{{ item.team?.name }}</div>
                <div class="mr-4">{{ item.token }}</div>
                <div class="">{{ item.chat_id }}</div>
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
    import {ref, defineExpose} from 'vue'
    import axios from "axios";

    const modal = modalStore();

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
        console.log(chats.value.filter((item)=> item.select));
    }


</script>

<style scoped>

</style>
