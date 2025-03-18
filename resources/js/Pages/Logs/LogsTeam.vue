<template>
    <Layout :title="title" :crew="crew" class="errors flex flex-col grow">
        <div class="flex mb-4">
            <div class="p-2 font-bold text-xl uppercase">Список логов</div>
            <div class="grow"></div>
            <div class="self-center mr-4">Настройка списка:</div>
            <SelectList placeholder="Нет настройки" :input="false" v-model:value="option" :minWidth="220" :list="getOptions()" @select="selectOption"></SelectList>
            <button class="square-button add" title="Сохранить текущие фильтры, колонки и сортировку в новую настройку" @click="createOptionBegin"></button>
            <button v-if="option?.guid" class="square-button save" title="Обновить текущие фильтры, колонки и сортировку настройки" @click="saveOption"></button>
            <button v-if="option?.guid" class="square-button delete" :title="'Удалить текущую настройку ' + option?.name" @click="removeOptionBegin"></button>
            <Button icon="options-pic" class="ml-4 mr-4" @click="modal.columns = true">Настройка колонок {{ countColumns() }}</Button>
            <Button icon="filter-pic" class="mr-4" @click="modal.filters = true">Фильтры {{ countFilters() }}</Button>
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
                <div v-if="shade" class="shade"></div>
            </PerfectScrollbar>
            <div class="flex p-2">
                <Button class="button mr-4 px-6">Пред.</Button>
                <div class="grow"></div>
                <Button class="button px-6">След.</Button>
            </div>
        </div>
    </Layout>
    <Columns :team="guid" v-model:columns="columns" type="log"></Columns>
    <Filters :team="guid" v-model:filters="fields" type="log" @filter="filtering"></Filters>
    <Sort :team="guid" v-model:sort="sort" :fields="fields" type="log" @confirm="filtering"></Sort>
    <SetName title="Введите наименование настройки" @complete="createOption"></SetName>
    <Question :title="question.title" :question="question.question" :type="question.type" v-model:visible="question.visible" @confirm="questionEnd"></Question>
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
    import Question from "@/Components/JSON/Question.vue";
    import axios from "axios";

    const props = defineProps({
        title: String,
        guid: String,
        crew: Object,
        logs: Array,
        sort: Array,
        filters: Object,
        columns: Array,
        options: Array,
        option: Object,
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
    let options = ref([]);
    let option = ref({});
    let question = ref({
        type: '',
        title: '',
        question: '',
        visible: false
    });
    let shade = ref(false);

    onMounted(()=>{
        list.value = props.logs;
        sort.value = props.sort;
        if(Object.keys(props.filters).length > 0){
            fields.value = props.filters;
        }
        if(props.columns.length > 0){
            columns.value = props.columns;
        }
        options.value = props.options;
        option.value = props.option;
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
        shade.value = true;
        axios.post('/logs/filter', {team: props.guid, filter: fields.value, sort: sort.value}).then(function (response){
            shade.value = false;
            if(response.data.logs){
                list.value = response.data.logs;
            }
        }).catch(function (error){
            shade.value = false;
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

    function countColumns(){
        let count = 0;
        for (let column of columns.value){
            if(column?.width !== 1 || column?.hidden){
                count += 1;
            }
        }
        return '(' + count + ')';
    }

    function createOptionBegin(){
        modal.setName = true;
    }

    function createOption(name){
        shade.value = true;
        axios.post('/error/options/create', {team: props.guid, name: name, filters: fields.value, sort: sort.value, columns: columns.value}).then(function (response){
            shade.value = false;
            if(response.data.options){
                options.value = response.data.options;
            }
            if(response.data.option){
                option.value = response.data.option;
            }
        }).catch(function (error){
            shade.value = false;
        });
    }

    function saveOption(){
        let data = option.value;
        shade.value = true;
        axios.post('/error/options/save', {team: props.guid, guid: data.guid, filters: fields.value, sort: sort.value, columns: columns.value}).then(function (response){
            shade.value = false;
            if(response.data.options){
                options.value = response.data.options;
            }
            if(response.data.option){
                option.value = response.data.option;
            }
        }).catch(function (error){
            shade.value = false;
        });
    }

    function selectOption(item){
        let data = Object.assign({team: props.guid, guid: item.guid});
        shade.value = true;
        axios.post('/error/options/change', data).then(function (response){
            shade.value = false;
            let data = response.data;
            if(data.filters){
                fields.value = data.filters;
            }
            if(data.sort){
                sort.value = data.sort;
            }
            if(data.columns){
                columns.value = data.columns;
            }
            if(data.errors){
                list.value = data.errors;
            }
        }).catch(function (error){
            shade.value = false;
        });
    }

    function removeOptionBegin(){
        question.value.visible = true;
        question.value.type = 'remove option';
        question.value.title = 'Удалиние настройки';
        question.value.question = 'Удалить настройку ' + option.value.name + '?';
    }

    function removeOption(){
        shade.value = true;
        axios.post('/error/options/delete', {team: props.guid, guid: option.value.guid}).then(function (response){
            shade.value = false;
            if(response.data.options){
                options.value = response.data.options;
            }
            if(response.data.option){
                option.value = response.data.option;
            }
            if(response.data.errors){
                list.value = response.data.errors;
            }
        }).catch(function (error){
            shade.value = false;
        });
    }

    function questionEnd(type){
        if(type === 'remove option'){
            removeOption();
        }
    }

    function getOptions(){
        let arr = [];
        if(option.value?.guid){
            arr.push({name: 'Нет настройки'});
        }
        options.value.forEach((item)=> arr.push(item));
        return arr;
    }

</script>

<style scoped>

</style>
