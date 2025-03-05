<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import {defineProps} from 'vue'

    defineProps({
        guid: String,
        crew: Object,
        errors: Array
    })

</script>

<template>
    <Layout :crew="crew">
        <div class="flex">
            <div class="p-2 font-bold uppercase">Список ошибок</div>
            <div class="grow"></div>
            <button class="button">Кнопка</button>
        </div>
        <div class="flex p-2">
            <div class="mr-2">Дата</div>
            <div class="mr-2">Имя</div>
            <div class="mr-2">ID</div>
            <div class="mr-2">Категория</div>
            <div class="mr-2">Отправитель</div>
            <div class="mr-2">Код</div>
            <div class="mr-2">Пользователь</div>
            <div class="mr-2">Устройство</div>
            <div class="mr-2">Город</div>
            <div class="mr-2">Регион</div>
            <div>Версия</div>
        </div>
        <div v-for="error in errors" class="flex p-2">
            <div class="mr-2">{{ error.date }}</div>
            <div class="mr-2">{{ error.name }}</div>
            <div class="mr-2">{{ error.guid }}</div>
            <div class="mr-2">{{ error.category }}</div>
            <div class="mr-2">{{ error.sender_name }}</div>
            <div class="mr-2">{{ error.code }}</div>
            <div class="mr-2">{{ error.user }}</div>
            <div class="mr-2">{{ error.device }}</div>
            <div class="mr-2">{{ error.city }}</div>
            <div class="mr-2">{{ error.region }}</div>
            <div>{{ error.version }}</div>
        </div>
    </Layout>
</template>

<style scoped>

</style>
