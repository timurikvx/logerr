<template>
    <Layout :title="title">
        <div>dashboard</div>
    </Layout>
</template>

<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import {defineProps} from 'vue'

    defineProps({
        title: String
    })

</script>



