<template>
    <Layout :title="title">
        <div>dashboard</div>
    </Layout>
</template>

<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import {defineProps, provide} from 'vue'

    const props = defineProps({
        title: String,
        short: {
            type: Boolean,
            default: false
        }
    });

    provide('short', props.short);

</script>



