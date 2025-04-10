<template>
    <Layout title="Настройка оповещений">
        <div class="notifications-page flex flex-col grow">
            <div class="flex mx-2 mt-2">
                <div class="p-4 grow text-center cursor-pointer" :class="{'active': tab === 'options'}" @click="tab = 'options'">Настройка оповещений</div>
                <div class="p-4 grow text-center cursor-pointer" :class="{'active': tab === 'chats'}" @click="tab = 'chats'">Телеграм чаты</div>
            </div>
            <div class="flex p-4 mx-2 mb-2 content grow">
                <div class="flex flex-col" v-if="tab === 'options'">
                    <div>

                    </div>
                </div>
                <div class="flex flex-col" v-if="tab === 'chats'">
                    <div class="flex">
                        <button class="button">Добавить чат телеграм</button>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import {ref} from 'vue'


    let tab = ref('chats');

</script>


<style scoped>

</style>
