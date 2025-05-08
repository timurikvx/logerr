<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components2/ActionMessage.vue';
import ActionSection from '@/Components2/ActionSection.vue';
import Checkbox from '@/Components2/Checkbox.vue';
import ConfirmationModal from '@/Components2/ConfirmationModal.vue';
import DangerButton from '@/Components2/DangerButton.vue';
import DialogModal from '@/Components2/DialogModal.vue';
import FormSection from '@/Components2/FormSection.vue';
import InputError from '@/Components2/InputError.vue';
import InputLabel from '@/Components2/InputLabel.vue';
import PrimaryButton from '@/Components2/PrimaryButton.vue';
import SecondaryButton from '@/Components2/SecondaryButton.vue';
import SectionBorder from '@/Components2/SectionBorder.vue';
import TextInput from '@/Components2/TextInput.vue';

const props = defineProps({
    tokens: Array,
    availablePermissions: Array,
    defaultPermissions: Array,
});

const createApiTokenForm = useForm({
    name: '',
    permissions: props.defaultPermissions,
});

const updateApiTokenForm = useForm({
    permissions: [],
});

const deleteApiTokenForm = useForm({});

const displayingToken = ref(false);
const managingPermissionsFor = ref(null);
const apiTokenBeingDeleted = ref(null);

const createApiToken = () => {
    createApiTokenForm.post('/user/api-tokens', {
        preserveScroll: true,
        onSuccess: () => {
            displayingToken.value = true;
            createApiTokenForm.reset();
        },
    });
};

const manageApiTokenPermissions = (token) => {
    updateApiTokenForm.permissions = token.abilities;
    managingPermissionsFor.value = token;
};

const updateApiToken = () => {
    updateApiTokenForm.put('/user/api-tokens/' + managingPermissionsFor.value.id, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (managingPermissionsFor.value = null),
    });
};

const confirmApiTokenDeletion = (token) => {
    apiTokenBeingDeleted.value = token;
};

const deleteApiToken = () => {
    console.log(apiTokenBeingDeleted.value.id);
    deleteApiTokenForm.delete('/user/api-tokens/' + apiTokenBeingDeleted.value.id, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (apiTokenBeingDeleted.value = null),
    });
};

</script>

<template>

    <div>
        <!-- Generate API Token -->
        <FormSection @submitted="createApiToken">
            <template #title>
                Создание API токена
            </template>

            <template #description>
                API-токены позволяют сторонним сервисам проходить аутентификацию в нашем приложении от вашего имени
            </template>

            <template #form>
                <!-- Token Name -->
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="name" value="Имя токена" />
                    <input id="name" v-model="createApiTokenForm.name" type="text" class="mt-1 input w-full" autofocus>
<!--                    <TextInput-->
<!--                        id="name"-->
<!--                        v-model="createApiTokenForm.name"-->
<!--                        type="text"-->
<!--                        class="mt-1 block w-full"-->
<!--                        autofocus-->
<!--                    />-->
                    <InputError :message="createApiTokenForm.errors.name" class="mt-2" />
                </div>

                <!-- Token Permissions -->
                <div v-if="availablePermissions.length > 0" class="col-span-6">
                    <InputLabel for="permissions" value="Права" />

                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="permission in availablePermissions" :key="permission">
                            <label class="flex items-center">
                                <Checkbox v-model:checked="createApiTokenForm.permissions" :value="permission" />
                                <span class="ms-2 text-sm text-gray-600">{{ permission }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </template>

            <template #actions>
<!--                <ActionMessage :on="createApiTokenForm.recentlySuccessful" class="me-3">-->
<!--                    Создан.-->
<!--                </ActionMessage>-->
                <button class="button" :disabled="createApiTokenForm.processing">Создать</button>
<!--                <PrimaryButton :class="{ 'opacity-25': createApiTokenForm.processing }" :disabled="createApiTokenForm.processing">-->
<!--                    Создать-->
<!--                </PrimaryButton>-->
            </template>
        </FormSection>

        <div v-if="tokens.length > 0">
            <SectionBorder />

            <!-- Manage API Tokens -->
            <div class="mt-10 sm:mt-0">
                <ActionSection>
                    <template #title>
                        Управление API токенами
                    </template>

                    <template #description>
                        Вы можете удалить любой из существующих токенов, если он больше не нужен.
                    </template>

                    <!-- API Token List -->
                    <template #content>
                        <div class="space-y-6">
                            <div v-for="token in tokens" :key="token.id" class="flex items-center justify-between">
                                <div class="break-all">
                                    {{ token.name }}
                                </div>

                                <div class="flex items-center ms-2">
                                    <div v-if="token.last_used_ago" class="text-sm text-gray-400">
                                        Last used {{ token.last_used_ago }}
                                    </div>

                                    <button
                                        v-if="availablePermissions.length > 0"
                                        class="cursor-pointer ms-6 text-sm text-gray-400 underline"
                                        @click="manageApiTokenPermissions(token)"
                                    >
                                        Права
                                    </button>

                                    <button class="cursor-pointer ms-6 text-sm text-red-500" @click="confirmApiTokenDeletion(token)">
                                        Удалить
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </ActionSection>
            </div>
        </div>

        <!-- Token Value Modal -->
        <DialogModal :show="displayingToken" @close="displayingToken = false">
            <template #title>
                API токен
            </template>

            <template #content>
                <div>
                    Пожалуйста, скопируйте ваш новый API-токен. Для вашей безопасности он больше не будет показан.
                </div>

                <div v-if="$page.props.jetstream.flash.token" class="mt-4 bg-gray-100 px-4 py-2 rounded font-mono text-sm text-gray-500 break-all">
                    {{ $page.props.jetstream.flash.token }}
                </div>
            </template>

            <template #footer>
                <button class="button" :disabled="createApiTokenForm.processing" @click="displayingToken = false">Закрыть</button>
<!--                <SecondaryButton @click="displayingToken = false">-->
<!--                    Close-->
<!--                </SecondaryButton>-->
            </template>
        </DialogModal>

        <!-- API Token Permissions Modal -->
        <DialogModal :show="managingPermissionsFor != null" @close="managingPermissionsFor = null">
            <template #title>
                Права API токена
            </template>

            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="permission in availablePermissions" :key="permission">
                        <label class="flex items-center">
                            <Checkbox v-model:checked="updateApiTokenForm.permissions" :value="permission" />
                            <span class="ms-2 text-sm text-gray-600">{{ permission }}</span>
                        </label>
                    </div>
                </div>
            </template>

            <template #footer>
                <button class="button mr-4" :disabled="createApiTokenForm.processing" @click="managingPermissionsFor = null">Отмена</button>
<!--                <SecondaryButton @click="managingPermissionsFor = null">-->
<!--                    Cancel-->
<!--                </SecondaryButton>-->
                <button class="button" type="submit" :disabled="createApiTokenForm.processing" @click="updateApiToken">Сохранить</button>
<!--                <PrimaryButton-->
<!--                    class="ms-3"-->
<!--                    :class="{ 'opacity-25': updateApiTokenForm.processing }"-->
<!--                    :disabled="updateApiTokenForm.processing"-->
<!--                    @click="updateApiToken"-->
<!--                >-->
<!--                    Save-->
<!--                </PrimaryButton>-->
            </template>
        </DialogModal>

        <!-- Delete Token Confirmation Modal -->
        <ConfirmationModal :show="apiTokenBeingDeleted != null" @close="apiTokenBeingDeleted = null">
            <template #title>
                Удаление API токена
            </template>

            <template #content>
                Вы уверены, что хотите удалить этот токен API?
            </template>

            <template #footer>
                <button class="button mr-4" :disabled="createApiTokenForm.processing" @click="apiTokenBeingDeleted = null">Отмена</button>
<!--                <SecondaryButton @click="apiTokenBeingDeleted = null">-->
<!--                    Cancel-->
<!--                </SecondaryButton>-->
                <button class="button red" :disabled="createApiTokenForm.processing" @click="deleteApiToken">Удалить</button>
<!--                <DangerButton-->
<!--                    class="ms-3"-->
<!--                    :class="{ 'opacity-25': deleteApiTokenForm.processing }"-->
<!--                    :disabled="deleteApiTokenForm.processing"-->
<!--                    @click="deleteApiToken"-->
<!--                >-->
<!--                    Delete-->
<!--                </DangerButton>-->
            </template>
        </ConfirmationModal>
    </div>
</template>
