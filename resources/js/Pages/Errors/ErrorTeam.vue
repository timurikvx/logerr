<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import {defineProps, ref} from 'vue'
    import Button from "@/Components/Button.vue";
    import { columnsStore } from '@/Store/Columns.js';
    import TableOptions from "@/Components/TableOptions.vue";

    defineProps({
        guid: String,
        crew: Object,
        errors: Array
    });

    const columns = columnsStore();

    let order = ref([
        {class: 'column7', name: 'Пользователь', column: 'user', width: 0},
        {class: 'column1', name: 'Дата', type: 'date', column: 'date', width: 0},
        {class: 'column2', name: 'Имя', column: 'name', width: 0},
        {class: 'column6', name: 'Код', column: 'code', width: 0},
        {class: 'column3', name: 'ID', column: 'guid', width: 0},
        {class: 'column5', name: 'Отправитель', column: 'sender_name', width: 0},
        {class: 'column4', name: 'Категория', column: 'category', width: 0},
        {class: 'column8', name: 'Устройство', column: 'device', width: 0},
        {class: 'column9', name: 'Город', column: 'city', width: 0},
        {class: 'column10', name: 'Регион', column: 'region', width: 0},
        {class: 'column11', name: 'Версия', column: 'version', width: 0},
        {class: 'column12', name: '', column: '', width: 0},
    ]);

    function sort(){
        swap(order.value, 2, 3);
    }

    function swap(arr, a, b) {
        arr[a] = arr.splice(b, 1, arr[a])[0];
    }

    function getValue(row, column){
        let value = row[column.column];
        if(column.type === 'date'){
            return (new Date(value)).toLocaleString().replace(',', '');
        }
        return value;
    }

</script>

<template>
    <Layout :crew="crew" class="errors flex flex-col grow">
        <div class="flex mb-4">
            <div class="p-2 font-bold uppercase">Список ошибок</div>
            <div class="grow"></div>
            <Button icon="options-pic" @click="columns.show = true">Настройка таблицы</Button>
            <Button icon="filter-pic">Фильтры</Button>
            <Button icon="sort-pic">Сортировка</Button>
            <button class="button" @click="sort">Кнопка</button>
        </div>
        <div class="table-field grow">
            <table>
                <thead>
                <tr class="cursor-pointer">
                    <td v-for="column in order" :class="column.class">{{ column.name }}</td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="error in errors">
                    <td v-for="column in order" :class="column.class">{{ getValue(error, column) }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </Layout>
    <TableOptions :columns="order"></TableOptions>
</template>

<style scoped>

</style>
