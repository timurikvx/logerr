<template>
    <Modal title="Сортировка" class="sort" v-model:visible="modal.sort">
        <div class="mb-4 flex">
            <SelectFromSlide :list="getList()" name="sort" @change="addSort">
                <button class="button" :disabled="sort.length >= 5">Добавить сортировку</button>
            </SelectFromSlide>
            <div class="grow"></div>
        </div>
        <div class="mb-4" v-if="sort.length > 0">
            <div v-for="(item, index) in sort" class="flex">
                <div class="field grow mr-2">{{ item.name }}</div>
                <div class="field mr-2" :class="{'active': !item.desc}" @click="setDesc(item, false)">Возр</div>
                <div class="field mr-2" :class="{'active': item.desc}" @click="setDesc(item, true)">Убыв</div>
                <div class="field" @click="remove(index)">Удалить</div>
            </div>
        </div>
        <div class="p-2" v-else>Нет сортировок</div>
        <div class="flex">
            <button class="button red mr-2" @click="reset">Очистить</button>
<!--            <button class="button green" @click="save">Сохранить</button>-->
            <div class="grow"></div>
            <button class="button" @click="confirm()">Применить</button>
        </div>
    </Modal>
</template>

<script setup>

    import Modal from "@/Components/Modal.vue";
    import {modalStore} from "@/Store/Modal.js";
    import {defineProps, defineEmits, computed} from 'vue';
    import SelectFromSlide from "@/Components/SelectFromSlide.vue";
    import axios from "axios";

    const modal = modalStore();
    const emits = defineEmits(['update:sort', 'confirm']);
    const props = defineProps({
        team: String,
        sort: Array,
        fields: Object,
        type: String
    });

    let sort = computed({
        get(){
            return props.sort;
        },
        set(value){
            emits('update:sort', value);
        }
    })

    function getList(){
        let list = [];
        for (let name in props.fields){
            let row = sort.value.find((row)=> row.field === name);
            if(row){
                continue;
            }
            let field = props.fields[name];
            list.push({field: name, name: field.name});
        }
        return list;
    }

    function confirm(){
        emits('confirm');
        modal.sort = false;
        save();
    }

    function setDesc(item, value){
        item.desc = value;
    }

    function addSort(val){
        sort.value.push({name: val.filter.name, field: val.filter.field, desc: false});
    }

    function remove(index){
        sort.value.splice(index, 1);
    }

    function reset(){
        sort.value = [];
        clear();
    }

    function save(){
        setTimeout(function (){
            axios.post('/' + props.type + '/options/set', {team: props.team, sort: props.sort}).then(function (response){
                if(response.data.result){
                    console.log('true');
                }
            })
        }, 20);
    }

    function clear(){
        axios.post('/' + props.type + '/options/clear', {team: props.team, field: 'sort'}).then(function (response){
            if(response.data.result){
                console.log('true');
            }
        })
    }

</script>

<style scoped>

</style>
