<script setup>

    import Header from "@/Layouts/Header.vue";
    import Sidebar from "@/Layouts/Sidebar.vue";
    import {defineProps} from 'vue'

    defineProps({
        crew: {
            type: Object,
            default: {}
        }
    })

</script>

<template>
    <div class="flex flex-col grow overflow-hidden">
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
