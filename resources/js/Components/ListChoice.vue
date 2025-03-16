<template>
    <Modal title="Выбор списка значений" class="list-choice" v-model:visible="visible" back_class="z-50">
        <div class="flex mb-4 relative">
            <input ref="input" type="text" placeholder="Поиск..." class="input grow" @input="search($event.target.value)">
            <div class="fixed listing z-10 flex flex-col overflow-hidden" :style="style" :key="key" v-show="list_visible"  @mouseleave="mouseleave" @mouseover="mouseover">
                <PerfectScrollbar>
                    <div v-for="item in getListChoice()" class="p-2 item cursor-pointer" @click="select(item)">
                        <div>{{ item }}</div>
                    </div>
                </PerfectScrollbar>
            </div>
        </div>
        <PerfectScrollbar class="flex flex-wrap values mb-4">
            <div :key="item" v-for="(item, index) in list" class="mr-2 mb-2 flex choice-item">
                <div class="grow self-center">{{ item }}</div>
                <button class="close ml-2" @click="remove(index)"></button>
            </div>
        </PerfectScrollbar>
        <div class="flex">
            <button class="button" @click="visible = false">Отменить</button>
            <div class="grow"></div>
            <button class="button" @click="confirm">Применить</button>
        </div>
    </Modal>
</template>

<script setup>

    import Modal from "@/Components/Modal.vue";
    import {defineProps, defineEmits, computed, ref, onMounted} from 'vue';
    import axios from "axios";

    const emits = defineEmits(['update:visible', 'update:list', 'complete']);
    const props = defineProps({
        title: {
            type: String,
            default: 'Выбор списка значений'
        },
        list: {
            type: Array,
            default: []
        },
        visible: Boolean,
        table: String,
        column: String
    });
    const input = ref(null);

    let list = computed({
        get(){
            return props.list;
        },
        set(value){
            emits('update:list', value);
        }
    });
    let visible = computed({
        get(){
            return props.visible;
        },
        set(value){
            emits('update:visible', value);
        }
    });
    let id = null;
    let choice = ref([]);
    let style = ref('');
    let key = ref(Math.random());
    let list_visible = ref(false);
    let into = ref(false);

    onMounted(()=>{
        document.addEventListener('click', click);
    })

    function search(text){
        show();
        clearTimeout(id);
        id = setTimeout(function (){
            axios.post('/choice', {value: text, type: props.table, field: props.column}).then(function (response){
                list_visible.value = true;
                choice.value = response.data.list;
            });
        }, 250);
    }

    function show(){
        const rect = input.value.getBoundingClientRect();
        let top = rect.top + rect.height;
        style.value = 'width: ' + rect.width + 'px; top: ' + top + 'px';
    }

    function select(item){
        list.value.push(item);
        key.value = Math.random();
        setTimeout(show, 20);
    }

    function getListChoice(){
        return choice.value.filter((item) => {
            return !list.value.includes(item);
        })
    }

    function remove(index){
        list.value.splice(index, 1);
    }

    function confirm(){
        visible.value = false;
        emits('complete', props.column);
    }

    function mouseleave(){
        into.value = false;
    }

    function mouseover(){
        into.value = true;
    }

    function click(){
        if(!into.value){
            list_visible.value = false;
        }
    }

</script>

<style scoped>

</style>
