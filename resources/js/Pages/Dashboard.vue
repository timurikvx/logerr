<template>
    <Layout :title="title" class="dashboard">
        <div class="flex">
            <div class="p-4 text-4xl">Панель управления</div>
            <div class="grow"></div>
            <div class="self-center uppercase font-bold mr-4">Команда:</div>
            <SelectList placeholder="Выберите команду" :input="false" v-model:value="team" :minWidth="320" :list="props.teams" @select="selectTeam" class="p-2 self-center"></SelectList>
        </div>
        <div v-if="props.teams.length === 0" class="grow flex">
            <div class="m-auto text-4xl">Вы не состоите ни в одной команде</div>
        </div>
        <div v-else-if="!team.guid" class="grow flex">
            <div class="m-auto text-4xl">Выберите команду для просмотра</div>
        </div>
        <perfect-scrollbar v-if="team.guid" class="grow p-4 report">
            <div class="text-2xl mb-4">Топ 5 ошибок за сегодня</div>
            <div class="flex flex-wrap wrapper">
                <div v-for="(name, index) in Object.keys(props.reports.today)" class="p-4 field flex flex-col grow" :class="getClass(index)">
                    <div class="mb-2 grow truncate">{{ name }}</div>
                    <div class="flex">
                        <div class="grow"></div>
                        <div class="text-3xl"> {{ props.reports.today[name] }}</div>
                    </div>
                </div>
            </div>
            <div class="text-2xl mb-4">Топ ошибок за 5 дней</div>
            <VueApexCharts ref="five_days_report" :series="five_days.series" :options="five_days.options"></VueApexCharts>
        </perfect-scrollbar>
    </Layout>
</template>

<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import {defineProps, onMounted, provide, ref} from 'vue'
    import SelectList from "@/Components/SelectList.vue";
    import axios from "axios";
    import VueApexCharts from "vue3-apexcharts";

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
    const five_days_report = ref(null);

    provide('short', props.short);

    onMounted(()=>{
        team.value = props.team;
        five_days.series = props.reports.five_days.series;
        five_days.options.xaxis.categories = props.reports.five_days.categories;
    });

    let team = ref({});

    function selectTeam(team){
        console.log(team);
        axios.post('/team/change', {team: team.guid}).then(function (response){
            console.log(response.data);
        }).catch(function (error){

        });
    }

    function getClass(index){
        return 'item-' + '' + index;
    }

    let five_days = {
        series: [],
        options: {
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    dataLabels: {
                        total: {
                            enabled: false,
                            //offsetX: 0,
                            // style: {
                            //     fontSize: '15px',
                            //     fontWeight: 900
                            // }
                        }
                    }
                },
            },
            grid: {
               show: false
            },
            stroke: {
                width: 1,
                colors: ['#fff']
            },
            title: {
                //text: 'Fiction Books Sales'
            },
            xaxis: {
                categories: [],
                //min: 100,
                labels: {
                    minHeight: '300px',
                }
                //categories: [2008, 2009, 2010, 2011, 2012, 2013, 2014],
                // labels: {
                //     formatter: function (val) {
                //         return val + "K"
                //     }
                // }
            },
            yaxis: {
                title: {
                    //text: 'ss'
                },
            },
            tooltip: {
                // y: {
                //     formatter: function (val) {
                //         return val + "K"
                //     }
                // }
            },
            theme: {
                mode: 'dark',
                palette: 'palette1',
            },
            //colors:['#8851fa', '#656565', '#9C27B0', '#d0338c', '#9aa5f7', '#52b944'],
            fill: {
                opacity: 1,
                //colors:['#8851fa', '#656565', '#9C27B0', '#d0338c', '#9aa5f7', '#52b944']
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                offsetX: 40
            }
        }
    }

</script>



