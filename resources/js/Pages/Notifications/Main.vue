<template>
    <Layout title="Настройка оповещений">
        <div class="notifications-page flex flex-col grow overflow-hidden">
            <div class="flex mx-2 mt-2">
                <div class="p-4 grow text-center cursor-pointer" :class="{'active': tab === 'options'}" @click="tab = 'options'">Настройка оповещений</div>
                <div class="p-4 grow text-center cursor-pointer" :class="{'active': tab === 'chats'}" @click="tab = 'chats'">Телеграм чаты</div>
            </div>
            <div class="flex p-4 mx-2 mb-2 content grow overflow-hidden">
                <div class="flex flex-col grow" v-if="tab === 'options'">
                    <div>

                    </div>
                </div>
                <div class="flex flex-col grow overflow-hidden" v-if="tab === 'chats'">
                    <div class="flex mb-4">
                        <button class="button mr-4" @click="modal.telegramChat = true">Новый телеграм чат</button>
                        <button class="button">Добавить из другой команды</button>
                    </div>
                    <perfect-scrollbar class="flex flex-col grow pr-4">
                        <div v-for="chat in chats" class="flex mb-4">
                            <div class="p-2 grow">{{ chat.name }}</div>
                            <div class="p-2 mr-4">{{ chat.guid }}</div>
                            <div class="p-2 mr-4">{{ chat.chat_id }}</div>
                            <button class="button mr-4">Изменить</button>
                            <button class="button red" @click="removeChatBegin(chat)">Удалить</button>
                        </div>
                    </perfect-scrollbar>
                </div>
            </div>

        </div>
    </Layout>
    <TelegramChatEdit :chat="chat" @save="saveChat"></TelegramChatEdit>
    <Question :question="question" v-model:visible="question.visible" @confirm="questionEnd"></Question>
</template>

<script setup>

//:title="question.title" :question="question.question" :type="question.type"
    import Layout from "@/Layouts/Layout.vue";
    import {ref, defineProps, onMounted} from 'vue'
    import {modalStore} from "@/Store/Modal.js";
    import TelegramChatEdit from "@/Components/TelegramChatEdit.vue";
    import Question from "@/Components/Question.vue";
    import axios from "axios";

    const modal = modalStore();
    const props = defineProps({
        chats: Array
    });

    let tab = ref('chats');
    let chat = ref({});
    let chats = ref([]);
    let question = ref({
        type: '',
        title: '',
        question: '',
        visible: false,
        valur: null
    });

    onMounted(()=>{
        chats.value = props.chats;
    })

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

</script>


<style scoped>

</style>
