<script setup>

    import { Head } from '@inertiajs/vue3';
    import Layout from "@/Layouts/Layout.vue";
    import RegisterForm from "@/Components/RegisterForm.vue";

</script>

<template>
    <Head title="Регистрация" />
    <Layout :sidebar="false" class="flex flex-col overflow-hidden">
        <div class="grow flex flex-col">
            <RegisterForm class="w-1/3">
                <div class="text-center py-3 font-bold text-2xl head">Регистрация</div>
            </RegisterForm>
        </div>
    </Layout>
</template>
