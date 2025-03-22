<template>
    <div class="notifications flex flex-col" v-show="modal.notifications">
        <div class="p-2 flex flex-col grow">
            <div class="text-2xl px-1 font-bold mb-4">Уведомления</div>
            <div class="flex list flex-col grow" v-if="notifications.list.length > 0">
                <div class="item" v-for="item in notifications.list" @click="select($event, item)">
                    <div class="mb-2 font-bold">{{ item.title }}</div>
                    <div class="truncate">{{ item.content }}</div>
                </div>
            </div>
            <div v-else class="flex flex-col grow">
                <div class="text-center my-auto empty select-none">Уведомлений нет</div>
            </div>
        </div>
    </div>
    <Modal :title="item.title" class="notification-item" v-model:visible="show">
        <div class="">{{ item.content }}</div>
        <div v-if="item.confirm" class="flex mt-4">
            <button class="button red" @click="close">Закрыть</button>
            <div class="grow"></div>
            <button class="button green" @click="confirm">Подтвердить</button>
        </div>
    </Modal>
</template>

<script setup>

    import {onMounted, ref} from "vue";
    import {modalStore} from "@/Store/Modal.js";
    import {notificationsStore} from "@/Store/Notifications.js";
    import Modal from "@/Components/Modal.vue";
    import axios from "axios";

    const modal = modalStore();
    const notifications = notificationsStore();

    let show = ref(false);
    let item = ref({});

    onMounted(()=>{
        document.addEventListener('click', click);
    });

    function click(e){
        let element = e.target;
        if(element.classList.contains('notification')){
            return;
        }
        if(element.closest('.notification-item')){
            return;
        }
        if(element.closest('.notifications')){
            return;
        }
        modal.notifications = false;
    }

    function select(e, notification){
        if(e.details < 2){
            return;
        }
        item.value = notification;
        show.value = true;
    }

    function close(){
        show.value = false;
        item.value = {};
    }

    function confirm(){
        show.value = false;
        axios.post('/notifications/confirm', item.value).then(function (response){
            if(response.data.list){
                notifications.list = response.data.list;
            }
        });
    }

</script>

<style scoped>

</style>
