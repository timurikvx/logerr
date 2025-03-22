<template>
    <Modal class="error-form" title="Ошибка" v-model:visible="modal.errors">
        <div class="text-center font-mono py-4">
            {{ modal.error }}
        </div>
    </Modal>
</template>

<script setup>

    import Modal from "@/Components/Modal.vue";
    import {modalStore} from "@/Store/Modal.js";
    import {useButtons} from "@/Packs/Buttons/index.js";

    const modal = modalStore();
    const buttons = useButtons();

    buttons.escape(function (){
        modal.errors = false;
        modal.error = '';
    });

</script>



<style scoped>

</style>
