<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from '@/Components2/ActionSection.vue';
import DangerButton from '@/Components2/DangerButton.vue';
import DialogModal from '@/Components2/DialogModal.vue';
import InputError from '@/Components2/InputError.vue';
import SecondaryButton from '@/Components2/SecondaryButton.vue';
import TextInput from '@/Components2/TextInput.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <ActionSection>
        <template #title>
            Удаление аккаунта
        </template>

        <template #description>
            Полное удаления аккаунта
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-300">
                После удаления вашего аккаунта все его ресурсы и данные будут удалены навсегда. Перед удалением аккаунта загрузите любые данные или информацию, которые вы хотите сохранить.
            </div>

            <div class="mt-5 flex">
                <div class="grow"></div>
                <button class="button full-red" @click="confirmUserDeletion">Удалить аккаунт</button>
<!--                <DangerButton @click="confirmUserDeletion">-->
<!--                    Delete Account-->
<!--                </DangerButton>-->
            </div>

            <!-- Delete Account Confirmation Modal -->
            <DialogModal :show="confirmingUserDeletion" @close="closeModal">
                <template #title>
                    Удаление аккаунта
                </template>

                <template #content>
                    Вы уверены, что хотите удалить свою учетную запись? После удаления вашей учетной записи все ее ресурсы и данные будут удалены навсегда. Введите пароль, чтобы подтвердить, что вы хотите навсегда удалить свою учетную запись.

                    <div class="mt-4">
                        <input
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 input w-3/4"
                            placeholder="Пароль"
                            autocomplete="current-password"
                            @keyup.enter="deleteUser"
                        />

                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                </template>

                <template #footer>
                    <button class="button full-red" :disabled="form.processing" @click="deleteUser">Удалить аккаунт</button>
<!--                    <SecondaryButton @click="closeModal">-->
<!--                        Cancel-->
<!--                    </SecondaryButton>-->
<!--                    -->
<!--                    <DangerButton-->
<!--                        class="ms-3"-->
<!--                        :class="{ 'opacity-25': form.processing }"-->
<!--                        :disabled="form.processing"-->
<!--                        @click="deleteUser">-->
<!--                        Удалить аккаунт-->
<!--                    </DangerButton>-->
                </template>
            </DialogModal>
        </template>
    </ActionSection>
</template>
