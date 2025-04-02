<template>
    <Layout :title="title" class="teams flex flex-col overflow-hidden grow">
        <div class="flex mb-2 px-2 pt-2">
            <div class="p-2 font-bold text-xl uppercase">Выбор команды просмотра ошибок</div>
            <div class="grow"></div>
        </div>
        <div class="flex flex-col grow table-field p-4 m-4 mt-0">
            <div class="flex flex-wrap">
                <a v-for="item in teams" class="flex mb-4 team" :href="'/errors/' + item.guid">
                    <div class="flex grow item">
                        <div class="grow self-center text-3xl px-6">{{ item.name }}</div>
                    </div>
                </a>
            </div>
        </div>
    </Layout>
</template>

<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import { modalStore } from '@/Store/Modal.js';
    import {defineProps, onMounted, provide, ref} from "vue";
    const store = modalStore();

    let teams = ref([]);

    const props = defineProps({
        title: String,
        short: {
            type: Boolean,
            default: false
        }
    });

    provide('short', props.short);

    onMounted(() => {
        axios.post('/team/list').then(function(response){
            teams.value = response.data.list;
        })
    });

</script>

<style scoped>

</style>
