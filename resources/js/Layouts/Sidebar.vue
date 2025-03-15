<template>
    <div class="sidebar flex flex-col p-4">
        <button class="button mb-2" @click="modal.createTeams = true">Создать команду</button>
        <div class="py-2">Ваши команды</div>
        <div class="flex flex-col">
            <div class="py-2 px-3 flex team mb-2" v-for="item in teams.list" :class="{'active': crew?.guid === item.guid}">
                <a class="grow" :href="'/' + item.guid + '/errors'">{{ item.name }}</a>
            </div>
        </div>
        <CreateTeam></CreateTeam>
    </div>
</template>

<script setup>

    import CreateTeam from "@/Modal/CreateTeam.vue";
    import {teamsStore} from '@/Store/Teams.js'
    import {modalStore} from "@/Store/Modal.js";
    import { onMounted, defineProps } from "vue";

    const modal = modalStore();
    const teams = teamsStore();

    defineProps({
        crew: {
            type: Object,
            default: {}
        }
    })

    onMounted(() => {
        axios.post('/team/list').then(function(response){
            teams.list = response.data.list;
        })
    })

</script>

<style scoped>

</style>
