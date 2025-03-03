<script setup>

    import Header from "@/Layouts/Header.vue";
    import Sidebar from "@/Layouts/Sidebar.vue";

</script>

<template>
    <div class="flex flex-col grow">
        <Header></Header>
        <div class="flex grow">
            <Sidebar></Sidebar>
            <div class="grow p-2">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
