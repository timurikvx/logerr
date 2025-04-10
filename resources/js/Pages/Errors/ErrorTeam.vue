<template>
    <Layout :title="title" :crew="crew" class="errors flex flex-col grow">
        <div class="flex mb-4 px-4 pt-4">
            <div class="p-2 font-bold text-xl uppercase">{{ head }}</div>
            <div class="grow"></div>
            <div class="self-center mr-4">Команда:</div>
            <SelectList :input="false" v-model:value="team" :minWidth="300" :list="props.teams" @select="selectTeam" class="mr-4"></SelectList>
            <div class="self-center mr-4">Настройка списка:</div>
            <SelectList placeholder="Нет настройки" :input="false" v-model:value="option" :minWidth="220" :list="getOptions()" @select="selectOption"></SelectList>
            <button class="square-button add" title="Сохранить текущие фильтры, колонки и сортировку в новую настройку" @click="createOptionBegin"></button>
            <button v-if="option?.guid" class="square-button save" title="Обновить текущие фильтры, колонки и сортировку настройки" @click="saveOption"></button>
            <button v-if="option?.guid" class="square-button delete" :title="'Удалить текущую настройку ' + option?.name" @click="removeOptionBegin"></button>
            <Button icon="options-pic" class="ml-4 mr-4" @click="modal.columns = true">Настройка колонок {{ countColumns() }}</Button>
            <Button icon="filter-pic" class="mr-4" @click="modal.filters = true">Фильтры {{ countFilters() }}</Button>
            <Button icon="sort-pic" @click="modal.sort = true">Сортировка {{ countSort() }}</Button>
        </div>
        <div class="table-field flex flex-col grow overflow-hidden m-4 mt-0">
            <div class="cursor-pointer grid head" :style="getGrid()">
                <div v-for="column in getColumns()" class="p-2" :class="column.class">{{ column.name }}</div>
                <div class="p-2"></div>
            </div>
            <PerfectScrollbar ref="scroll" class="grow">
                <div v-for="error in list">
                    <div class="grid line" :style="getGrid()">
                        <div v-for="column in getColumns()" class="p-2" :title="getValue(error, column)" :class="column.class">{{ getValue(error, column) }}</div>
                        <div class="p-2 flex toggle" @click="error.show = !error.show">
                            <div class="decoding self-center m-auto" :class="[{'collapse-icon': error.show}, {'expand-icon': !error.show}]"></div>
                        </div>
                    </div>
                    <div v-if="error.show" class="p-2 data">
                        <div v-if="error.data" class="flex flex-col">
                            <div class="border-bottom mb-4 head-data">Данные:</div>
                            <DataPrint :data="error.data"></DataPrint>
                        </div>
                        <div v-if="error.query" class="flex flex-col my-4">
                            <div class="mb-2 border-bottom head-data">Запрос:</div>
                            <DataPrint :data="error.query"></DataPrint>
                        </div>
                        <div v-if="error.response" class="flex flex-col mt-4">
                            <div class="mb-2 border-bottom head-data">Ответ:</div>
                            <DataPrint :data="error.response"></DataPrint>
                        </div>
                    </div>
                </div>
                <div v-if="shade" class="shade"></div>
            </PerfectScrollbar>
            <div class="flex p-2 mt-2 paginate">
                <button class="button mr-2 px-6" @click="paginating(paginate.prev)">Пред.</button>
                <button class="self-center py-2 px-4 mr-2 link" v-for="link in paginate.links" @click="paginating(link.page)" :class="{active: link.active}">{{ link.label }}</button>
                <button class="button px-6" @click="paginating(paginate.next)">След.</button>
            </div>
        </div>
    </Layout>
    <Columns :team="guid" v-model:columns="columns" type="error"></Columns>
    <Filters :team="guid" v-model:filters="fields" type="error" @filter="filtering"></Filters>
    <Sort :team="guid" v-model:sort="sort" :fields="fields" type="error" @confirm="filtering"></Sort>
    <SetName title="Введите наименование настройки" @complete="createOption"></SetName>
    <Question :title="question.title" :question="question.question" :type="question.type" v-model:visible="question.visible" @confirm="questionEnd"></Question>
</template>

<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import {defineProps, onMounted, provide, ref} from 'vue'
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
        list: Array,
        sort: Array,
        filters: Object,
        columns: Array,
        options: Array,
        option: Object,
        paginate: Object,
        time: Number,
        head: String,
        prefix: String,
        short: {
            type: Boolean,
            default: false
        },
        teams: Array,
        team: Object
    });
    const modal = modalStore();
    const buttons = useButtons();
    const scroll = ref(null);

    let columns = ref([]);
    let fields = ref({});
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
    let paginate = ref({});
    let team = ref({});

    onMounted(()=>{
        list.value = props.list;
        sort.value = props.sort;
        fields.value = props.filters;
        columns.value = props.columns;
        options.value = props.options;
        option.value = props.option;
        paginate.value = props.paginate;
        team.value = props.team;
        console.log(props.team);
    });

    provide('short', props.short);

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
        axios.post('/' + props.prefix + '/filter', {team: props.guid, filter: fields.value, sort: sort.value}).then(function (response){
            shade.value = false;
            scroll.value.$el.scrollTop = 0;
            if(response.data.list){
                list.value = response.data.list;
            }
            if(response.data.paginate){
                paginate.value = response.data.paginate;
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
        axios.post('/' + props.prefix + '/options/create', {team: props.guid, name: name, filters: fields.value, sort: sort.value, columns: columns.value}).then(function (response){
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
        axios.post('/' + props.prefix + '/options/save', {team: props.guid, guid: data.guid, filters: fields.value, sort: sort.value, columns: columns.value}).then(function (response){
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
        axios.post('/' + props.prefix + '/options/change', data).then(function (response){
            shade.value = false;
            scroll.value.$el.scrollTop = 0;
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
            if(data.list){
                list.value = data.list;
            }
            if(data.paginate){
                paginate.value = data.paginate;
            }
        }).catch(function (error){
            shade.value = false;
        });
    }

    function removeOptionBegin(){
        question.value.visible = true;
        question.value.type = 'remove option';
        question.value.title = 'Удаление настройки';
        question.value.question = 'Удалить настройку ' + option.value.name + '?';
    }

    function removeOption(){
        shade.value = true;
        axios.post('/' + props.prefix + '/options/delete', {team: props.guid, guid: option.value.guid}).then(function (response){
            shade.value = false;
            if(response.data.options){
                options.value = response.data.options;
            }
            if(response.data.option){
                option.value = response.data.option;
            }
            if(response.data.list){
                list.value = response.data.list;
            }
            if(response.data.paginate){
                paginate.value = response.data.paginate;
            }
            if(response.data.filters){
                fields.value = response.data.filters;
            }
            if(response.data.columns){
                columns.value = response.data.columns;
            }
            if(response.data.sort){
                sort.value = response.data.sort;
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

    function paginating(page){
        shade.value = true;
        //, filter: fields.value, sort: sort.value
        axios.post('/' + props.prefix + '/page', {page: page, team: props.guid}).then(function (response){
            shade.value = false;
            scroll.value.$el.scrollTop = 0;
            if(response.data.list){
                list.value = response.data.list;
            }
            if(response.data.paginate){
                paginate.value = response.data.paginate;
            }
        }).catch(function (error){
            shade.value = false;
        });
    }

    function selectTeam(team){
        axios.post('/' + props.prefix + '/team/change', {team: team.guid}).then(function (response){
            shade.value = false;
            scroll.value.$el.scrollTop = 0;
            console.log(response.data);
        }).catch(function (error){
            shade.value = false;
        });
    }

</script>

<style scoped>

</style>
