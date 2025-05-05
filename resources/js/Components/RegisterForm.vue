<template>
    <form @submit.prevent="submit" class="auth m-auto">
        <div>
            <slot></slot>
        </div>
        <div class="p-4">
            <div class="flex flex-col mb-2">
                <label class="mb-1">Имя</label>
                <input class="input" v-model="form.name">
            </div>
            <div v-if="form.errors.name" class="text-red-400 mb-4">{{ form.errors.email }}</div>
            <div class="flex flex-col mb-2">
                <label class="mb-1">Фамилия</label>
                <input class="input" v-model="form.surname">
            </div>
            <div v-if="form.errors.surname" class="text-red-400 mb-4">{{ form.errors.surname }}</div>
            <div class="flex flex-col mb-2">
                <label class="mb-1">Почта</label>
                <input class="input" v-model="form.email">
            </div>
            <div v-if="form.errors.email" class="text-red-400 mb-4">{{ form.errors.email }}</div>
            <div class="flex flex-col mb-2">
                <label class="mb-1">Пароль</label>
                <input class="input" type="password" v-model="form.password">
            </div>
            <div v-if="form.errors.password" class="text-red-400 mb-4">{{ form.errors.password }}</div>
            <div class="flex flex-col mb-2">
                <label class="mb-1">Подтверждение</label>
                <input class="input" type="password" v-model="form.password_confirmation">
            </div>
            <div class="flex flex-col mb-2">
                <label class="flex">
                    <input class="input self-center" type="checkbox" v-model="form.remember">
                    <span class="self-center ml-2">Запомнить меня</span>
                </label>
            </div>
            <div class="flex">
                <div class="grow"></div>
                <a class="underline self-center mr-4" href="/login">Уже зарегистрированы?</a>
                <button class="button" :disabled="form.processing">Зарегистрироваться</button>
            </div>
        </div>
    </form>
</template>

<script setup>

    import { useForm } from '@inertiajs/vue3';

    defineProps({
        canResetPassword: Boolean,
        status: String,
    });

    const form = useForm({
        name: '',
        surname: '',
        email: '',
        password: '',
        password_confirmation: '',
        terms: false,
    });
    const submit = () => {
        form.post('/register', {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    };

</script>

