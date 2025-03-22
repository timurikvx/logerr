<template>
    <div class="notifications flex flex-col" v-show="modal.notifications">
        <div class="p-2 flex flex-col grow">
            <div class="text-2xl px-1 font-bold">Уведомления</div>
            <div class="flex flex-col grow">
                <div class="text-center my-auto empty select-none">Уведомлений нет</div>
            </div>
        </div>
    </div>
</template>

<script setup>

    import {onMounted} from "vue";
    import {modalStore} from "@/Store/Modal.js";
5
    const modal = modalStore();

    onMounted(()=>{
        document.addEventListener('click', click);
    });

    function click(e){
        let element = e.target;
        if(element.classList.contains('notification')){
            return;
        }
        if(element.closest('.notifications')){
            return;
        }
        modal.notifications = false;
    }


</script>

<style scoped>

</style>
