<template>
    <Modal title="Фильтры" class="filters" v-model:visible="store.filters">
        <div class="mb-4">
            <div class="grid list mb-2 title">
                <div class="self-center" title="Использование отбора">Исп.</div>
                <div class="self-center p-2">Имя отбора</div>
                <div class="self-center p-2 cursor-pointer text-center">Сравнение</div>
                <div></div>
                <div class="self-center p-2">Значение фильтра</div>
            </div>
            <div v-for="(filter, name) in filters" class="grid list mb-2">
                <div class="flex">
                    <input type="checkbox" class="self-center m-1" v-model="filter.use">
                </div>
                <div class="self-center font-bold p-2">{{ filter.name }}</div>
                <SelectFromSlide :list="data[filter.type]" :name="name" @change="changeEqual">
                    <div class="self-center equal cursor-pointer">{{ getEqual(filter) }}</div>
                </SelectFromSlide>
                <div></div>
                <div v-if="filter.equal === 'list' || filter.equal === 'not_list'" class="flex">
                    <div class="add-to-list add" @click="showChoice(name, filter)"></div>
                    <div class="grow flex truncate choice-list">
                        <div class="choice-item mr-2" v-for="row in filter.list">{{ row }}</div>
                    </div>
                </div>
                <div v-else class="flex">
                    <input :type="filter.type" class="input grow" v-model="filter.value" @input="inputValue($event, name)">
                    <input v-if="filter.equal === 'between'" :type="filter.type" class="input grow ml-2" v-model="filter.value2" @input="inputValue($event,name)">
                </div>
            </div>
        </div>
        <div class="flex">
            <button class="button red mr-2" @click="reset">Сбросить</button>
<!--            <button class="button green" @click="save">Сохранить</button>-->
            <div class="grow"></div>
            <button class="button" @click="confirm">Применить</button>
        </div>
    </Modal>
    <ListChoice v-model:visible="list_choice" :list="choice" table="error" :column="field" @complete="choiceComplete"></ListChoice>
</template>

<script setup>

    import Modal from "@/Components/Modal.vue";
    import {modalStore} from "@/Store/Modal.js";
    import SelectFromSlide from "@/Components/SelectFromSlide.vue";
    import ListChoice from "@/Components/ListChoice.vue";
    import {defineProps, onMounted, ref, defineEmits, computed} from 'vue';
    import axios from "axios";

    const store = modalStore();
    const props = defineProps({
        team: String,
        filters: Object,
        type: String
    });
    const emits = defineEmits(['update:filters', 'filter']);

    let data = {};
    let values = {};
    let result = ref({});
    let filters = computed({
        get(){
            return props.filters;
        },
        set(value){
            emits('update:filters', value);
        }
    });
    let list_choice = ref(false);
    let choice = ref([]);
    let field = ref('');

    onMounted(()=>{
        axios.post('/filters/get').then(function (response){
            data = response.data;
            fill();
        });
    });

    function close(){
        store.filters = false;
    }

    function fill(){
        for (let name of Object.keys(props.filters)){
            let filter = props.filters[name];
            let list = data[filter.type];
            values[name] = list[0];
        }
    }

    function changeEqual(val){
        let item = filters.value[val.name];
        item.equal = val.filter.value;
        item.use = true;
    }

    function inputValue(e, name){
        let item = filters.value[name];
        item.use = true;
    }

    function getEqual(filter){
        let list = data[filter.type];
        let equal = list.find((row)=> row.value === filter.equal);
        if(!equal){
            return 'Равно';
        }
        return equal.name;
    }

    function confirm(){
        save();
        emits('filter');
        close();
    }

    function reset(){
        for (let i of Object.keys(filters.value)){
            let filter = filters.value[i];
            filter.use = false;
            filter.value = null;
            filter.value2 = null;
            filter.equal = null;
            filter.list = null;
        }
        clear();
    }

    function showChoice(name, item){
        list_choice.value = true;
        field.value = name;
        if(item.list){
            choice.value = item.list;
        }else{
            choice.value = [];
        }
    }

    function choiceComplete(name){
        filters.value[name].list = choice.value;
    }

    function save(){
        axios.post('/' + props.type + '/options/set', {team: props.team, filters: props.filters}).then(function (response){
            if(response.data.result){
                console.log('true');
            }
        })
    }

    function clear(){
        axios.post('/' + props.type + '/options/clear', {team: props.team, field: 'filters'}).then(function (response){
            if(response.data.result){
                console.log('true');
            }
        })
    }

</script>

<style scoped>

</style>
