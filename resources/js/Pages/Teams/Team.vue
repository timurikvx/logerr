<template>
    <Layout :title="title" class="team-item">
        <div class="flex mb-4">
            <input v-if="change" type="text" class="input text-3xl grow" v-model="name">
            <div v-else class="p-2 text-3xl">{{ team.name }}</div>
            <div class="grow"></div>
            <div v-if="change" class="flex">
                <button class="button mr-4" @click="change = false">Отменить</button>
                <button  class="button" @click="save">Сохранить</button>
            </div>
            <button v-else class="button" @click="change = true">Изменить имя</button>
        </div>
        <PerfectScrollbar class="flex flex-col">
            <div class="flex border-bottom mb-2">
                <div class="p-2 text-2xl">Учатники команды</div>
                <div class="grow"></div>
                <div class="w-1/4 flex mr-4">
                    <input type="text" class="self-center input grow" placeholder="Поиск" v-model="search" @input="searching">
                </div>
            </div>
            <div v-for="member in filtered" class="flex mb-2 border-bottom pb-2">
                <div class="flex member p-2">
                    <div class="mr-3">{{ member.user.surname }}</div>
                    <div>{{ member.user.name }}</div>
                </div>
                <div class="grow"></div>
                <div class="flex mr-6" v-if="member.user.id === props.user">
                    <div class="self-center font-bold p-2 px-4">{{ getRole(member) }}</div>
                </div>
                <div class="roles flex mr-6" v-else>
                    <div v-for="role in Object.keys(roles)" class="role py-2 px-4 mr-2" :class="{active: member.roles.includes(role)}" @click="changeRole(member, role)">
                        {{ roles[role] }}
                    </div>
                </div>
                <div v-if="member.user.id === props.user" class="p-2 px-8 font-bold mr-4">Это вы</div>
                <button v-else class="button red mr-4" @click="excludeBegin(member.user)">Исключить</button>
            </div>
        </PerfectScrollbar>
        <Question :title="question.title" :question="question.question" :type="question.type" v-model:visible="question.visible" @confirm="questionEnd"></Question>
    </Layout>
</template>

<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import {defineProps, onMounted, ref} from 'vue';
    import axios from "axios";
    import Question from "@/Components/JSON/Question.vue";

    const props = defineProps({
        title: String,
        team: Object,
        members: Array,
        roles: Object,
        user: Number
    });

    let change = ref(false);
    let name = ref('');
    let team = ref({});
    let members = ref([]);
    let filtered = ref([]);
    let question = ref({
        type: '',
        title: '',
        question: '',
        visible: false
    });
    let excludable = ref({});
    let search = ref('');

    onMounted(()=>{
        name.value = props.team.name;
        team.value = props.team;
        members.value = props.members;
        filtered.value = props.members;
    });

    function save(){
        axios.post('/team/save', {name: name.value, id: team.value.id}).then(function(){
            team.value.name = name.value;
            change.value = false;
        })
    }

    function changeRole(member, role){
        axios.post('/team/role/change', {role: role, user: member.user.id, team: team.value.id}).then(function (response){
            if(response.data.result){
                member.roles = [role];
            }
        });
    }

    function excludeBegin(user){
        excludable.value = user;
        question.value.visible = true;
        question.value.type = 'exclude';
        question.value.title = 'Исключение пользователя из команды';
        question.value.question = 'Исключенить пользователя из команды ' + team.value.name + '?';
    }

    function exclude(user){
        axios.post('/team/exclude', {user: user.id, team: team.value.id}).then(function (response){
            if(response.data.members){
                members.value = response.data.members;
            }
        });
    }

    function questionEnd(type){
        if(type === 'exclude'){
            exclude(excludable.value);
        }
    }

    function getRole(member){
        let role = member.roles[0];
        return props.roles[role];
    }

    function searching(){
        filtered.value = members.value.filter((item) => {
            let full_name = item.user.surname + ' ' + item.user.name;
            return full_name.indexOf(search.value) !== -1;
        })
    }

</script>

<style scoped>

</style>
