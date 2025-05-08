<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components2/ActionMessage.vue';
import FormSection from '@/Components2/FormSection.vue';
import InputError from '@/Components2/InputError.vue';
import InputLabel from '@/Components2/InputLabel.vue';
import PrimaryButton from '@/Components2/PrimaryButton.vue';
import TextInput from '@/Components2/TextInput.vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <FormSection @submitted="updatePassword">
        <template #title>
            Обновление пароля
        </template>

        <template #description>
            Для обеспечения безопасности убедитесь, что в вашей учетной записи используется длинный случайный пароль.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="current_password" value="Current Password" />
                <input
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 input w-full"
                    autocomplete="current-password"
                />
                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="password" value="New Password" />
                <input
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 input w-full"
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 input w-full"
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <button class="button px-8" type="submit" :disabled="form.processing">Сохранить</button>
<!--            <ActionMessage :on="form.recentlySuccessful" class="me-3">-->
<!--                Saved.-->
<!--            </ActionMessage>-->

<!--            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">-->
<!--                Save-->
<!--            </PrimaryButton>-->
        </template>
    </FormSection>
</template>
