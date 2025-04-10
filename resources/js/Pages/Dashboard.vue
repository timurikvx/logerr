<template>
    <Layout :title="title" class="dashboard">
        <div class="flex">
            <div class="p-4 text-4xl">Панель управления</div>
            <div class="grow"></div>
            <div class="self-center uppercase font-bold mr-4">Команда:</div>
            <SelectList placeholder="Выберите команду" :input="false" v-model:value="team" :minWidth="320" :list="props.teams" @select="selectTeam" class="p-2 self-center"></SelectList>
        </div>
        <div v-if="!team.guid" class="grow flex">
            <div class="m-auto text-4xl">Выберите команду для просмотра</div>
        </div>
        <perfect-scrollbar v-if="team.guid" class="grow p-4 report">
            <div class="text-2xl mb-4">Топ 5 ошибок за сегодня</div>
            <div class="flex flex-wrap wrapper">
                {{ team }}
<!--                <div v-for="item in props.reports.today" class="p-4 field flex flex-col grow">-->
<!--                    <div class="mb-4 font-bold text-2xl grow">{{ item.team }}</div>-->
<!--                    <div v-for="(error, name) in item.data" class="flex mb-2">-->
<!--                        <div class="mr-4 grow truncate">{{ name }}</div>-->
<!--                        <div class="text-xl"> {{ error }}</div>-->
<!--                    </div>-->
<!--                </div>-->
            </div>
        </perfect-scrollbar>
    </Layout>
</template>

<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import {defineProps, onMounted, provide, ref} from 'vue'
    import SelectList from "@/Components/SelectList.vue";
    import axios from "axios";
    //import VueApexCharts from "vue3-apexcharts";

    const props = defineProps({
        title: String,
        short: {
            type: Boolean,
            default: false
        },
        reports: {
            type: Object,
            default: {}
        },
        teams: Array,
        team: Object
    });

    provide('short', props.short);

    onMounted(()=>{
        //setTimeout(chart_all.value.refresh, 330);
       // console.log(props.reports);
        team.value = props.team;
    });

    let team = ref({});

    function selectTeam(team){
        console.log(team);
        axios.post('/team/change', {team: team.guid}).then(function (response){
            console.log(response.data);
        }).catch(function (error){

        });
    }

</script>



