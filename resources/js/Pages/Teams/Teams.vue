<template>
    <Layout :title="title" class="teams flex flex-col overflow-hidden grow">
        <div class="flex mb-2 px-4 pt-4">
            <div class="p-2 font-bold text-xl uppercase">Список команд</div>
            <div class="grow"></div>
            <button class="button mb-2" @click="modal.createTeams = true">Создать команду</button>
        </div>
        <div class="flex flex-col grow table-field m-4 mt-0">
            <div class="flex flex-wrap p-4 -mr-4">
                <div v-for="item in teams" class="flex mb-4 team">
                    <div class="flex grow item" :class="{'active': crew?.guid === item.guid}">
                        <div class="self-center">
                            <div class="grow mb-2 text-2xl px-4">{{ item.name }}</div>
                            <div class="px-4 text-purple-300">{{ item.guid }}</div>
                        </div>
                    </div>
                    <div class="flex flex-col refs">
                        <a v-if="item.roles.includes('admin')" class="grow p-2 text-center btn flex" :href="'/teams/' + item.guid">
                            <div class="m-auto">Управление</div>
                        </a>
                        <a v-if="item.roles.includes('manager') || item.roles.includes('admin')" class="grow p-2 text-center btn flex" @click="inviting(item)">
                            <div class="m-auto">Пригласить</div>
                        </a>
                    </div>
                    <div class="flex flex-col refs">
                        <a class="grow p-2 text-center btn flex" :href="'/errors/' + item.guid">
                            <div class="m-auto">Ошибки</div>
                        </a>
                        <a class="grow p-2 text-center btn flex" :href="'/logs/' + item.guid">
                            <div class="m-auto">Логи</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
    <CreateTeam @created="created"></CreateTeam>
</template>

<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import {defineProps, onMounted, provide, ref} from "vue";
    import {modalStore} from "@/Store/Modal.js";
    import {inviteStore} from "@/Store/Invite.js";
    import CreateTeam from "@/Modal/CreateTeam.vue";

    const modal = modalStore();
    const invite = inviteStore();

    let teams = ref([]);

    const props = defineProps({
        title: String,
        crew: {
            type: Object,
            default: {}
        },
        short: {
            type: Boolean,
            default: false
        }
    });

    provide('short', props.short);

    onMounted(() => {
        axios.post('/team/list').then(function(response){
            teams.value = response.data.list;
        })
    });

    function created(list){
        teams.value = list;
    }

    function inviting(item){
        invite.team = item;
        invite.show = true;
    }

</script>

<style scoped>

</style>
