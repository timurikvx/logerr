<template>
    <Layout title="Оповещение" class="">
        <div class="m-4 flex flex-col grow main-back p-4">
            <CreateNotification :edit="true" v-model:notification="notification" v-model:fields="fields" :columns="columns" :chats="chats"></CreateNotification>
        </div>
    </Layout>
</template>

<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import CreateNotification from "@/Pages/Notifications/CreateNotification.vue";
    import {defineProps, onMounted, ref} from 'vue';

    const props = defineProps({
        chats: Array,
        option: Object,
        columns: Array
    });

    onMounted(()=>{
        option.value = props.option;
        notification.value = JSON.parse(JSON.stringify(option.value));
        delete notification.value['fields'];
        fields.value = props.option.fields;
    });

    let option = ref({});
    let notification = ref({});
    let fields = ref([]);

</script>

<style scoped>

</style>
