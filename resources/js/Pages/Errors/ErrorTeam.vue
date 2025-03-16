<template>
    <Layout :crew="crew" class="errors flex flex-col grow">
        <div class="flex mb-4">
            <div class="p-2 font-bold text-xl uppercase">Список ошибок</div>
            <div class="grow"></div>
            <SelectList :input="false" v-model:value="option" :minWidth="220" :list="options" class=""></SelectList>
            <button class="square-button save" title="Сохранить текущие фильтры, колонки и сортировку" @click="saveOptionBegin"></button>
            <button class="square-button delete mr-4" :title="'Удалить текущую настройку ' + option.name"></button>
            <Button icon="options-pic" @click="modal.setName = true">Настройки</Button>
            <Button icon="options-pic" @click="modal.columns = true">Настройка таблицы</Button>
            <Button icon="filter-pic" @click="modal.filters = true">Фильтры {{ countFilters() }}</Button>
            <Button icon="sort-pic" @click="modal.sort = true">Сортировка {{ countSort() }}</Button>
        </div>
        <div class="table-field flex flex-col grow overflow-hidden">
            <div class="cursor-pointer grid head" :style="getGrid()">
                <div v-for="column in getColumns()" class="p-2" :class="column.class">{{ column.name }}</div>
                <div class="p-2"></div>
            </div>
            <PerfectScrollbar class="grow">
                <div v-for="error in list">
                    <div class="grid line" :style="getGrid()">
                        <div v-for="column in getColumns()" class="p-2" :title="getValue(error, column)" :class="column.class">{{ getValue(error, column) }}</div>
                        <div class="p-2 flex" @click="error.show = !error.show">
                            <div class="decoding self-center" :class="[{'collapse-icon': error.show}, {'expand-icon': !error.show}]"></div>
                        </div>
                    </div>
                    <div v-if="error.show" class="p-2 data">
                        <DataPrint :data="error.data"></DataPrint>
                    </div>
                </div>
            </PerfectScrollbar>
            <div class="flex p-2">
                <button class="button px-6">Пред.</button>
                <div class="grow"></div>
                <button class="button px-6">След.</button>
            </div>
        </div>
    </Layout>
    <Columns :columns="columns"></Columns>
    <Filters v-model:filters="fields" @filter="filtering"></Filters>
    <Sort :sort="sort" :fields="fields" @confirm="filtering"></Sort>
    <SetName title="Введите наименование настройки" @complete="saveOption"></SetName>
</template>

<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import {defineProps, onMounted, ref} from 'vue'
    import Button from "@/Components/Button.vue";
    import Columns from "@/Components/Columns.vue";
    import DataPrint from "@/Components/JSON/DataPrint.vue";
    import Filters from "@/Components/Filters.vue";
    import SetName from "@/Components/SetName.vue";
    import {modalStore} from "@/Store/Modal.js";
    import Sort from "@/Components/Sort.vue";
    import {useButtons} from '@/Packs/Buttons';
    import SelectList from "@/Components/SelectList.vue";
    import axios from "axios";
    import {s} from "../../../../public/build/assets/app-CWDMpo7l.js";

    const props = defineProps({
        guid: String,
        crew: Object,
        errors: Array,
        sort: Array,
        filters: Object,
        columns: Array,
        options: Array,
    });
    const modal = modalStore();
    const buttons = useButtons();

    let columns = ref([
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
    let sort = ref([]);
    let list = ref([]);
    let options = ref([
        {name: 'Настройка 1', guid: '3g3g3g3g3g3g'},
        {name: 'Настройка для того чтобы', guid: 'nt55h4h43g2g2'},
        {name: 'Настройка этого', guid: '23423fdsassd'},
        {name: 'Настройка 2', guid: 'fdgfgddsfdsf223242'},
        {name: 'Для просмотра ошибок таких то', guid: '23324323232'},
    ]);
    let option = ref({name: 'Настройка этого', guid: '23423fdsassd'});

    onMounted(()=>{
        list.value = props.errors;
        sort.value = props.sort;
        if(Object.keys(props.filters).length > 0){
            fields.value = props.filters;
        }
        if(props.columns.length > 0){
            columns.value = props.columns;
        }
    });

    buttons.escape(function (){
        if(modal.columns){
            modal.columns = false;
        }
        if(modal.filters){
            modal.filters = false;
        }
        if(modal.sort){
            modal.sort = false;
        }
    }, {stop: true, prevent: true});

    function getValue(row, column){
        let value = row[column.column];
        if(column.type === 'date'){
            return (new Date(value)).toLocaleString().replace(',', '');
        }
        return value;
    }

    function getGrid(){
        let text = '';
        for (let column of columns.value){
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

    function getColumns(){
        let arr = [];
        for (let row of columns.value){
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

    function filtering(){
        axios.post('/' + props.guid + '/errors/filter', {filter: fields.value, sort: sort.value}).then(function (response){
            if(response.data.errors){
                list.value = response.data.errors;
            }
        });
    }

    function countFilters(){
        let count = 0;
        for (let i of Object.keys(fields.value)){
            count += fields.value[i].use? 1: 0;
        }
        return '(' + count + ')';
    }

    function countSort(){
        return '(' + sort.value.length + ')';
    }

    function saveOptionBegin(){
        modal.setName = true;
    }

    function saveOption(name){
        axios.post('/error/options/save', {name: name, filters: fields.value, sort: sort.value, columns: columns.value});
    }

    function removeOption(){

    }

</script>

<style scoped>

</style>
