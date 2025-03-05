<template>
    <div class="sidebar flex flex-col p-2">
        <button class="button" @click="store.createTeam = true">Создать команду</button>
        <div class="py-2">Ваши команды</div>
        <div class="flex flex-col">
            <div class="p-2 flex" v-for="item in store.list" :class="{'active': crew?.guid === item.guid}">
                <a class="grow" :href="'/' + item.guid + '/errors'">{{ item.name }}</a>
            </div>
        </div>
        <CreateTeam></CreateTeam>
    </div>
</template>

<script setup>

    import CreateTeam from "@/Modal/CreateTeam.vue";
    import { modalStore } from '@/Store/Teams.js'
    import { onMounted, defineProps } from "vue";

    const store = modalStore();

    defineProps({
        crew: {
            type: Object,
            default: {}
        }
    })

    onMounted(() => {
        axios.post('/team/list').then(function(response){
            store.list = response.data.list;
        })
    })

</script>

<style scoped>

</style>
