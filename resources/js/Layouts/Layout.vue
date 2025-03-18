<script setup>

    import Header from "@/Layouts/Header.vue";
    import Sidebar from "@/Layouts/Sidebar.vue";
    import {Head} from '@inertiajs/vue3';

    import {defineProps} from 'vue'

    const props = defineProps({
        title: {
            type: String,
            default: ''
        },
        crew: {
            type: Object,
            default: {}
        },
        class: {
            type: String,
            default: ''
        }
    });

    function getClass(){
        return props.class;
    }

</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta name="description" content="Page description">
    </Head>
    <div class="flex flex-col grow overflow-hidden" :class="getClass()">
        <Header></Header>
        <div class="flex grow overflow-hidden">
            <Sidebar :crew="crew"></Sidebar>
            <div class="grow p-4 flex flex-col overflow-hidden">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
