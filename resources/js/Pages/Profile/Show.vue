<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components2/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import Layout from "@/Layouts/Layout.vue";
import ApiTokenManager from "@/Pages/API/Partials/ApiTokenManager.vue";

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
    tokens: Array,
    availablePermissions: Array,
    defaultPermissions: Array,
});
</script>

<template>
    <Layout :sidebar="false" >
        <div class="max-w-7xl mx-auto py-4 sm:px-2 lg:px-4 flex flex-col overflow-hidden">
            <h2 class="font-semibold text-2xl mb-6">
                Профиль
            </h2>
            <perfect-scrollbar class="pr-4">
                <div >
                    <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                        <UpdateProfileInformationForm :user="$page.props.auth.user" />

                        <SectionBorder />
                    </div>

                    <div v-if="$page.props.jetstream.canUpdatePassword">
                        <UpdatePasswordForm class="mt-10 sm:mt-0" />

                        <SectionBorder />
                    </div>

                    <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                        <TwoFactorAuthenticationForm
                            :requires-confirmation="confirmsTwoFactorAuthentication"
                            class="mt-10 sm:mt-0"
                        />

                        <SectionBorder />
                    </div>

                    <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" />



                    <div>
                        <SectionBorder />
                        <ApiTokenManager
                            :tokens="tokens"
                            :available-permissions="availablePermissions"
                            :default-permissions="defaultPermissions"
                        />
                    </div>

                    <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                        <SectionBorder />

                        <DeleteUserForm class="mt-10 sm:mt-0" />
                    </template>


                </div>
            </perfect-scrollbar>
        </div>
    </Layout>
<!--    <AppLayout title="Profile">-->
<!--        <template #header>-->
<!--            <h2 class="font-semibold text-xl text-gray-800 leading-tight">-->
<!--                Profile-->
<!--            </h2>-->
<!--        </template>-->

<!--        <div>-->
<!--            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">-->
<!--                <div v-if="$page.props.jetstream.canUpdateProfileInformation">-->
<!--                    <UpdateProfileInformationForm :user="$page.props.auth.user" />-->

<!--                    <SectionBorder />-->
<!--                </div>-->

<!--                <div v-if="$page.props.jetstream.canUpdatePassword">-->
<!--                    <UpdatePasswordForm class="mt-10 sm:mt-0" />-->

<!--                    <SectionBorder />-->
<!--                </div>-->

<!--                <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">-->
<!--                    <TwoFactorAuthenticationForm-->
<!--                        :requires-confirmation="confirmsTwoFactorAuthentication"-->
<!--                        class="mt-10 sm:mt-0"-->
<!--                    />-->

<!--                    <SectionBorder />-->
<!--                </div>-->

<!--                <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" />-->

<!--                <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">-->
<!--                    <SectionBorder />-->

<!--                    <DeleteUserForm class="mt-10 sm:mt-0" />-->
<!--                </template>-->
<!--            </div>-->
<!--        </div>-->
<!--    </AppLayout>-->
</template>
