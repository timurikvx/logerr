<script setup>

    import Layout from "@/Layouts/Layout.vue";
    import LoginForm from "@/Components/LoginForm.vue";

    const props = defineProps({
        title: String,
        canLogin: Boolean,
        canRegister: Boolean,
        laravelVersion: String,
        phpVersion: String,
    });

</script>

<template>
    <Layout :title="title" :sidebar="false" class="p-0">
        <div class="flex grow index">
            <div class="w-1/2 grow left flex flex-col mr-4 p-4">
                <div class="flex mb-8">
                    <div class="first font-bold text-6xl">Log</div>
                    <div class="second font-bold text-6xl">Err</div>
                </div>
                <div class="mono json-text p-4 grow text-lg">
                    <div class="quote">{</div>
                    <div class="flex ml-6 mb-1">
                        <div class="property">"name"</div>
                        <div class="mx-1 equal">:</div>
                        <div class="value">"Сервис хранения ошибок и логов"</div>
                        <div class="">,</div>
                    </div>
                    <div class="flex ml-6">
                        <div class="property">"description"</div>
                        <div class="mx-1 equal">:</div>
                        <div class="value">"Инструмент для хранения, анализа, оповещения и просмотра результатов сбора ваших ошибок и логирования"</div>
                    </div>
                    <div class="flex ml-6">
                        <div class="property">"capabilities"</div>
                        <div class="mx-1 equal">:</div>
                        <div class="flex flex-col">
                            <div class="quote">[</div>
                            <div class="flex">
                                <div class="ml-6 value">"Оповещение в телеграм об ошибках"</div>
                                <div>,</div>
                            </div>
                            <div class="flex">
                                <div class="ml-6 value">"Настройка оповещений"</div>
                                <div>,</div>
                            </div>
                            <div class="flex">
                                <div class="ml-6 value">"dsad"</div>
                                <div>,</div>
                            </div>
                            <div>]</div>
                        </div>
                    </div>
                    <div class="quote">}</div>
                </div>
            </div>
            <div class="w-1/2 grow flex right">
                <div class="w-1/2 m-auto">
                    <LoginForm>
                    </LoginForm>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style>

</style>
