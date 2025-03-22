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
        <Notifications></Notifications>
        <Invite></Invite>
        <Error></Error>
    </div>
</template>

<script setup>

    import Header from "@/Layouts/Header.vue";
    import Sidebar from "@/Layouts/Sidebar.vue";
    import {Head} from '@inertiajs/vue3';
    import Notifications from "@/Layouts/Notifications.vue";
    import Invite from "@/Components/Invite.vue";
    import Error from "@/Components/Error.vue";
    import {notificationsStore} from "@/Store/Notifications.js";

    import {defineProps, onMounted} from 'vue'

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
    const notifications = notificationsStore();

    onMounted(()=>{
        notifications.get();
        notifications.intervalGet(30000);
    })

    function getClass(){
        return props.class;
    }

</script>

<style scoped>

</style>
