<template>
    <Layout :sidebar="false" class="flex flex-col overflow-hidden">
        <div class="grow flex flex-col">
            <LoginForm class="w-1/3">
                <div class="text-center py-3 font-bold text-2xl head">Авторизация</div>
            </LoginForm>
        </div>
    </Layout>
</template>

<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import LoginForm from "@/Components/LoginForm.vue";

</script>

