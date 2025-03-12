<template>
    <Modal title="Создать команду" class="create-team flex flex-col" v-model:visible="modal.createTeams" @close="clear">
        <div class="grow flex mb-2">
            <div class="flex flex-col grow">
                <div class="mb-1">Имя</div>
                <input class="input" type="text" v-model="form.name">
            </div>
        </div>
        <div class="grow flex mb-2">
            <div class="flex flex-col grow">
                <div class="mb-1">Идентификатор</div>
                <input class="input" type="text" v-model="form.guid">
            </div>
        </div>
        <div v-if="errors.length > 0" class="mb-2 error">
            <div v-for="error in errors" class="mb-1">{{ error }}</div>
        </div>
        <div class="flex mt-2">
            <div class="grow"></div>
            <button class="button" @click="create">Создать</button>
        </div>
    </Modal>
</template>

<script setup>

    import Modal from "@/Components/Modal.vue";
    import {ref} from 'vue'
    import {teamsStore} from '@/Store/Teams.js';
    import {modalStore} from '@/Store/Modal.js'

    const modal = modalStore();
    const teams = teamsStore();

    let form = ref({
        name: '',
        guid: ''
    })
    let errors = ref([]);

    function create(){
        axios.post('/team/create', form.value).then(function (response){
            if(response.data.errors){
                errors.value = response.data.errors
            }else{
                teams.list = response.data.list;
                modal.createTeam = false;
                clear();
            }
        });
    }

    function clear(){
        form.value = {
            name: '',
            guid: ''
        }
        errors.value = [];
    }

</script>

<style scoped>

</style>
