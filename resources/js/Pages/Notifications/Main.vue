<template>
    <Layout title="Настройка оповещений">
        <div class="notifications-page flex flex-col grow overflow-hidden">
            <div class="flex mx-2 mt-2">
                <div class="p-4 grow text-center cursor-pointer" :class="{'active': tab === 'options'}" @click="tab = 'options'">Настройка оповещений</div>
                <div class="p-4 grow text-center cursor-pointer" :class="{'active': tab === 'chats'}" @click="tab = 'chats'">Телеграм чаты</div>
            </div>
            <div class="flex p-4 mx-2 mb-2 content grow overflow-hidden">
                <div class="flex flex-col grow" v-if="tab === 'options'">
                    <div class="flex mb-4" v-if="!modal.newNotification">
                        <button class="button mr-4" @click="modal.newNotification = true">Новое оповещение</button>
                    </div>
                    <CreateNotification :chats="chats" v-else></CreateNotification>
                </div>
                <div class="flex flex-col grow overflow-hidden" v-if="tab === 'chats'">
                    <div class="flex mb-4">
                        <button class="button mr-4" @click="createChat()">Новый телеграм чат</button>
                        <button class="button" @click="getChatToCopy()">Добавить из другой команды</button>
                    </div>
                    <perfect-scrollbar class="flex flex-col grow pr-4">
                        <div v-for="chat in chats" class="flex mb-4">
                            <div class="p-2 grow">{{ chat.name }}</div>
                            <div class="p-2 mr-4">{{ chat.guid }}</div>
                            <div class="p-2 mr-4">{{ chat.chat_id }}</div>
                            <button class="button mr-4" @click="change(chat)">Изменить</button>
                            <button class="button red" @click="removeChatBegin(chat)">Удалить</button>
                        </div>
                    </perfect-scrollbar>
                </div>
            </div>
        </div>
    </Layout>
    <TelegramChatEdit ref="telegramChats" :create="create" @save="saveChat"></TelegramChatEdit>
    <CopyTelegramChat ref="telegramChatsCopy" @copied="copied"></CopyTelegramChat>
    <Question :question="question" v-model:visible="question.visible" @confirm="questionEnd"></Question>

</template>

<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import {ref, defineProps, onMounted} from 'vue'
    import {modalStore} from "@/Store/Modal.js";
    import TelegramChatEdit from "@/Components/TelegramChatEdit.vue";
    import Question from "@/Components/Question.vue";
    import axios from "axios";
    import CopyTelegramChat from "@/Pages/Notifications/CopyTelegramChat.vue";
    import CreateNotification from "@/Pages/Notifications/CreateNotification.vue";

    const modal = modalStore();
    const props = defineProps({
        chats: Array
    });
    const telegramChats = ref(null);
    const telegramChatsCopy = ref(null);

    let tab = ref('options');
    let chats = ref([]);
    let question = ref({
        type: '',
        title: '',
        question: '',
        visible: false,
        valur: null
    });
    let create = ref(true);

    onMounted(()=>{
        chats.value = props.chats;
    })

    function createChat(){
        modal.telegramChat = true;
        create.value = true;
    }

    function saveChat(list){
        chats.value = list;
    }

    function removeChatBegin(chat){
        question.value.visible = true;
        question.value.value = chat;
        question.value.type = 'remove chat';
        question.value.title = 'Удаление чата';
        question.value.question = 'Удалить чат ' + chat.name + '?';
    }

    function questionEnd(question){
        if(question.type === 'remove chat'){
            removeChat(question.value);
        }
    }

    function removeChat(chat){
        axios.post('/telegram/chat/remove', {guid: chat.guid}).then(function (response){
            chats.value = response.data.list;
        })
    }

    function change(item){
        create.value = false;
        modal.telegramChat = true;
        telegramChats.value.update(item);
    }

    function getChatToCopy(){
        modal.telegramChatCopy = true;
        telegramChatsCopy.value.update();
    }

    function copied(list){
        chats.value = list;
    }

</script>


<style scoped>

</style>
