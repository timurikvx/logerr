<template>
    <Layout :title="title" class="">
        <div class="p-4 text-4xl">Панель управления</div>
        <perfect-scrollbar class="grow p-4 report">
            <div class="text-2xl mb-4">Топ 5 ошибок по командам за сегодня</div>
            <div class="flex flex-wrap wrapper">
                <div v-for="item in props.reports.today" class="p-4 field flex flex-col grow">
                    <div class="mb-4 font-bold text-2xl grow">{{ item.team }}</div>
                    <div v-for="(error, name) in item.data" class="flex mb-2">
                        <div class="mr-4 grow truncate">{{ name }}</div>
                        <div class="text-xl"> {{ error }}</div>
                    </div>
                </div>
            </div>
        </perfect-scrollbar>
    </Layout>
</template>

<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import {defineProps, onMounted, provide, ref} from 'vue'
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
        }
    });

    provide('short', props.short);

    //let chart_all = ref(null);

    onMounted(()=>{
        //setTimeout(chart_all.value.refresh, 330);
       // console.log(props.reports);
    })

    function click(){
        //chart_all.value.refresh();
    }

    // function getOptions(key){
    //     let option = JSON.parse(JSON.stringify(all_errors.value.options));
    //     option.xaxis.categories = props.reports.all[key].columns;;
    //     console.log(option);
    //     return option;
    // }
    //
    // function getSeries(key){
    //     return [
    //         {
    //             name: key,
    //             data: props.reports.all[key].series
    //         }
    //     ]
    // }
    //
    // //////////////////////////////////////////////////////////////////
    //
    // let all_errors = ref({
    //     series: [
    //         {
    //             name: "Desktops",
    //             data: [183, 178, 165, 98, 98, 97, 97, 93, 92, 91]
    //         },
    //         {
    //             name: "Shits",
    //             data: [23, 88, 35, 45, 34, 1, 19, 45, 77]
    //         }
    //     ],
    //     options: {
    //         chart: {
    //             height: 350,
    //             type: 'bar',
    //             stacked: false,
    //             // animations: {
    //             //     enabled: true,
    //             //     easing: 'linear',
    //             //     dynamicAnimation: {
    //             //         speed: 1000
    //             //     }
    //             // },
    //             toolbar: {
    //                 show: false
    //             },
    //             zoom: {
    //                 enabled: true
    //             },
    //             events: {
    //                 mounted: function(chartContext, config) {
    //
    //                 }
    //             },
    //         },
    //         plotOptions: {
    //             bar:{
    //                 horizontal: true,
    //             }
    //         },
    //         dataLabels: {
    //             enabled: true,
    //             style: {
    //                 fontSize: '10px',
    //                 colors: ['#8851fa', '#d0338c', '#ecff91', '#9aa5f7', '#52b944', '#656565']
    //             }
    //         },
    //         stroke: {
    //             show: true,
    //             curve: 'smooth',
    //             colors: ['#8851fa', '#d0338c', '#ecff91', '#9aa5f7', '#52b944', '#656565']
    //         },
    //         // title: {
    //         //     text: 'ТОП 10 ошибок',
    //         //     align: 'left'
    //         // },
    //         markers: {
    //             size: 0
    //         },
    //         xaxis: {
    //             categories: ["Ошибка связи MindBox", "Ошибка создания инт. заказа", "Загрузка заказов в офисе", "Регламентное загрузки накладных", "Ошибка отправки регистра заданий", "Ошибка обработки ячеек постамата", "ОтправкаОстатковКафка", "Накладные: начало загрузки", "(Offline.RollbackOrderTransaction) MindBox:400", "Ошибка обмена заказами покупателя"],
    //         },
    //         yaxis: {
    //             //max: 100
    //         },
    //         legend: {
    //             show: false,
    //             horizontalAlign: 'left',
    //         },
    //         fill: {
    //             type: 'gradient',
    //             //colors: ['#8851fa', '#d0338c', '#ecff91', '#9aa5f7', '#52b944', '#656565']
    //         },
    //         tooltip: {
    //             enabled: false,
    //             theme: 'dark',
    //         }
    //     }
    // })

</script>



