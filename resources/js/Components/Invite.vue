<template>
    <Modal class="invite" title="Приглашение пользователя в команду" v-model:visible="invite.show">
        <div class="flex flex-col mb-4">
            <div class="mb-1">Команда</div>
            <div class="text-2xl truncate">{{ invite.team.name }}</div>
        </div>
        <div class="flex flex-col mb-4">
            <div class="mb-1">Почта</div>
            <input type="text" class="input" v-model="email">
        </div>
        <div class="flex flex-col">
            <button class="button" @click="inviting">
                <span class="mx-auto">Пригласить</span>
            </button>
        </div>
    </Modal>
</template>

<script setup>

    import Modal from "@/Components/Modal.vue";
    import {inviteStore} from "@/Store/Invite.js";
    import {modalStore} from "@/Store/Modal.js";
    import {ref} from 'vue'
    import axios from "axios";

    const invite = inviteStore();
    const modal = modalStore();

    let email = ref('');

    function inviting(){
        if(email.value.trim().length === 0){
            return;
        }
        axios.post('/team/invite', {guid: invite.team.guid, email: email.value.trim()}).then(function (response){
            if(response.data.error){
                modal.setError(response.data.error);
                return;
            }
            email.value = '';
            invite.show = false;
        });

    }

    //let visible = ref(true);

</script>

<style scoped>

</style>
