<template>
    <Layout :crew="crew" class="errors flex flex-col grow">
        <div class="flex mb-4">
            <div class="p-2 font-bold text-xl uppercase">Список ошибок</div>
            <div class="grow"></div>
            <Button icon="options-pic" @click="columns.show = true">Настройка таблицы</Button>
            <Button icon="filter-pic" @click="filter.show = true">Фильтры</Button>
            <Button icon="sort-pic">Сортировка</Button>
        </div>
        <div class="table-field flex flex-col grow overflow-hidden">
            <div class="cursor-pointer grid head" :style="getGrid()">
                <div v-for="column in getOrder()" class="p-2" :class="column.class">{{ column.name }}</div>
                <div class="p-2"></div>
            </div>
            <PerfectScrollbar>
                <div v-for="error in errors">
                    <div class="grid line" :style="getGrid()">
                        <div v-for="column in getOrder()" class="p-2" :title="getValue(error, column)" :class="column.class">{{ getValue(error, column) }}</div>
                        <div class="p-2 flex" @click="error.show = !error.show">
                            <div class="decoding self-center" :class="[{'collapse-icon': error.show}, {'expand-icon': !error.show}]"></div>
                        </div>
                    </div>
                    <div v-if="error.show" class="p-2 data">
                        <DataPrint :data="error.data"></DataPrint>
                    </div>
                </div>
            </PerfectScrollbar>
        </div>
    </Layout>
    <TableOptions :columns="order"></TableOptions>
    <Filters v-model:filters="fields" @filter="filtering"></Filters>
</template>

<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import {defineProps, ref} from 'vue'
    import Button from "@/Components/Button.vue";
    import { columnsStore } from '@/Store/Columns.js';
    import {filtersStore} from "@/Store/Filters.js";
    import TableOptions from "@/Components/TableOptions.vue";
    import DataPrint from "@/Components/JSON/DataPrint.vue";
    import Filters from "@/Components/Filters.vue";

    const props = defineProps({
        guid: String,
        crew: Object,
        errors: Array
    });

    const columns = columnsStore();
    const filter = filtersStore();

    let order = ref([
        {class: 'column1', name: 'Дата', type: 'date', column: 'date', width: 1},
        {class: 'column2', name: 'Имя', column: 'name', width: 1},
        {class: 'column3', name: 'ID', column: 'guid', width: 1},
        {class: 'column4', name: 'Категория', column: 'category', width: 1},
        {class: 'column5', name: 'Подкатегория', column: 'sub_category', width: 1},
        {class: 'column6', name: 'Отправитель', column: 'sender_name', width: 1},
        {class: 'column7', name: 'Код', column: 'code', width: 1},
        {class: 'column8', name: 'Пользователь', column: 'user', width: 1},
        {class: 'column9', name: 'Устройство', column: 'device', width: 1},
        {class: 'column10', name: 'Город', column: 'city', width: 1},
        {class: 'column11', name: 'Регион', column: 'region', width: 1},
        {class: 'column12', name: 'Версия', column: 'version', width: 1},
        {class: 'column13', name: 'Длительность', column: 'duration', width: 1},
    ]);

    let fields = ref({
        date: {'use': false, 'name': 'Дата', 'type': 'datetime-local', 'equal': null, 'value': null, 'value2': null, 'list': null},
        name: {'use': false, 'name': 'Имя', 'type': 'text', 'equal': null, 'value': null, 'value2': null, 'list': null},
        category: {'use': false, 'name': 'Категория', 'type': 'text', 'equal': null, 'value': null, 'value2': null, 'list': null},
        sub_category: {'use': false, 'name': 'Подкатегория', 'type': 'text', 'equal': null, 'value': null, 'value2': null, 'list': null},
        user: {'use': false, 'name': 'Пользователь', 'type': 'text', 'equal': null, 'value': null, 'value2': null, 'list': null},
        device: {'use': false, 'name': 'Устройство', 'type': 'text', 'equal': null, 'value': null, 'value2': null, 'list': null},
        city: {'use': false, 'name': 'Город', 'type': 'text', 'equal': null, 'value': null, 'value2': null, 'list': null},
        region: {'use': false, 'name': 'Регион', 'type': 'text', 'equal': null, 'value': null, 'value2': null, 'list': null},
        version: {'use': false, 'name': 'Версия', 'type': 'text', 'equal': null, 'value': null, 'value2': null, 'list': null},
        duration: {'name': 'Длительность', 'type': 'number', 'equal': null, 'value': null, 'value2': null, 'list': null},
    });
    let filters = ref({});
    let sort = ref([]);

    function getValue(row, column){
        let value = row[column.column];
        if(column.type === 'date'){
            return (new Date(value)).toLocaleString().replace(',', '');
        }
        return value;
    }

    function getGrid(){
        let text = '';
        for (let column of order.value){
            if(column.hidden){
                continue;
            }
            if(column.column === 'date'){
                text += 'minmax(150px, ' + column.width + 'fr) ';
            }else{
                text += column.width + 'fr ';
            }

        }
        return 'grid-template-columns: ' + text.trim() + ' 50px';
    }

    function getOrder(){
        let arr = [];
        for (let row of order.value){
            if(row.hidden){
                continue;
            }
            if(!row.column){
                continue;
            }
            arr.push(row);
        }
        return arr;
    }

    function filtering(filter){
        filters.value = filter;
        axios.post('/' + props.guid + '/errors/filter', {filter: filters.value, sort: sort.value}).then(function (response){
            console.log(response.data);
        });
    }

</script>

<style scoped>

</style>
