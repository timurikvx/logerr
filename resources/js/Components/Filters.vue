<template>
    <Modal title="Фильтры" class="filters" v-model:visible="store.show">
        <div class="mb-4">
            <div class="grid list mb-2 title">
                <div class="self-center">Исп.</div>
                <div class="self-center p-2">Имя отбора</div>
                <div class="self-center p-2 cursor-pointer">Сравнение</div>
                <div class="self-center p-2">Значение</div>
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
                <input :type="filter.type" class="input" v-model="filter.value" @input="inputValue(name)">
            </div>
        </div>
        <div class="flex">
            <button class="button mr-4" @click="close()">Отменить</button>
            <button class="button red">Сбросить</button>
            <div class="grow"></div>
            <button class="button" @click="confirm">Применить</button>
        </div>
    </Modal>
</template>

<script setup>

    import Modal from "@/Components/Modal.vue";
    import {filtersStore} from "@/Store/Filters.js";
    import SelectFromSlide from "@/Components/SelectFromSlide.vue";
    import {defineProps, onMounted, ref, defineEmits, computed} from 'vue';

    const store = filtersStore();

    const props = defineProps({
        filters: Object
    });

    const emits = defineEmits(['update:filters']);

    let data = {};
    let values = {};
    let result = ref({});
    let filters = computed({
        get(){
            return props.filters;
        },
        set(val){
            emits('update:filters', val);
        }
    })

    onMounted(()=>{
        axios.post('/filters').then(function (response){
            data = response.data;
            fill();
        });
    });

    function close(){
        store.show = false;
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

    function inputValue(name){
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
        console.log(filters.value);
    }

</script>

<style scoped>

</style>
