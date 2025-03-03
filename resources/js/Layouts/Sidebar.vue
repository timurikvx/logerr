<script setup>

    import CreateTeam from "@/Modal/CreateTeam.vue";
    import { modalStore } from '@/Store/Modal.js'
    import { onMounted, ref } from "vue";

    const store = modalStore();

    let teams = ref([]);

    onMounted(() => {
        axios.post('/team/list').then(function(response){
            teams.value = response.data;
        })
    })

</script>

<template>
    <div class="sidebar flex flex-col p-2">
        <button class="button" @click="store.visible = true">Создать команду</button>
        <div>Ваши команды</div>
        <div>
            <div v-for="item in teams">
                <div>{{ item.name }}</div>
            </div>
        </div>
        <CreateTeam></CreateTeam>
    </div>

</template>

<style scoped>

</style>
