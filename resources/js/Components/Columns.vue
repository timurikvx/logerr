<template>
    <Modal title="Настройка таблицы" class="table-options" v-model:visible="store.columns">
        <PerfectScrollbar class="flex flex-col pr-4 mb-4">
            <div v-for="(column, key) in columns" :key="column.column" :data-order="key + 1" class="flex mb-2 drag relative" @dragstart="dragstart" @dragend="dragend" @dragover="dragover">
                <div class="column flex grow mr-2 cursor-pointer">
                    <div class="capture mr-4" draggable="true"></div>
                    <div class="mr-4 grow">{{ column.name }}</div>
                </div>
                <div class="flex mr-4">
                    <button v-if="column.hidden" class="visible column-hidden" @click="toggleShowColumns(column, false)">Скрыто</button>
                    <button v-else class="visible" @click="toggleShowColumns(column, true)">Скрыть</button>
                </div>
                <div class="flex">
                    <div class="self-center mr-4">Ширина</div>
                    <div class="mr-2">
                        <Number class="width" :min="1" :max="100" v-model:value="column.width"></Number>
                    </div>
                    <div class="self-center icon return" title="Сбросить" @click="resetColumn(column)"></div>
                </div>
            </div>
        </PerfectScrollbar>
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
    import {defineProps, defineEmits, computed, ref} from 'vue'
    import Number from "@/Components/Number.vue";
    import {modalStore} from "@/Store/Modal.js";
    import axios from "axios";

    const store = modalStore();
    const props = defineProps({
        team: String,
        columns: Array,
        type: String
    });
    const emits = defineEmits(['update:columns', 'confirm']);

    let column = ref(null);
    let activeElement = null;
    let columns = computed({
        get(){
            return props.columns;
        },
        set(value){
            emits('update:columns', value);
        }
    })

    function toggleShowColumns(column, value){
        column.hidden = value;
    }

    function resetColumn(column){
        column.width = 1;
    }

    function dragstart(e){
        let parent = e.target.closest('.drag');
        parent.classList.add('selected');
        activeElement = parent;
    }

    function dragend(e){
        let parent = e.target.closest('.drag');
        parent.classList.remove('selected');
        activeElement = null;
    }

    function dragover(e){
        e.preventDefault();
        let element = document.elementFromPoint(e.clientX, e.clientY);
        let parent = element.closest('.drag');
        if(parent === activeElement){
            return;
        }
        let rect = parent.getBoundingClientRect();
        let half = Math.floor(rect.height / 2);
        let order = parent.getAttribute('data-order');
        if(rect.y + half > e.clientY){
            order--;
        }
        let current_order = activeElement.getAttribute('data-order');
        console.log('orders: ', current_order, order);
        if(current_order !== order){
            swap(columns.value, order - 1, current_order - 1);
            activeElement.setAttribute('data-order', order);
            //console.log('columns.value', columns.value);
        }
    }

    function swap(arr, a, b) {
        arr[a] = arr.splice(b, 1, arr[a])[0];
    }

    function reset(){
        for (let column of columns.value){
            column.hidden = false;
            column.width = 1;
        }
        clear();
    }

    function save(){
        axios.post('/' + props.type + '/options/set', {team: props.team, columns: columns.value}).then(function (response){
            if(response.data.result){
                console.log('true');
            }
        })
    }

    function confirm(){
        save();
        store.columns = false;
        emits('confirm');
    }

    function clear(){
        axios.post('/' + props.type + '/options/clear', {team: props.team, field: 'columns'}).then(function (response){
            if(response.data.result){
                console.log('true');
            }
        })
    }

</script>

<style scoped>

</style>
