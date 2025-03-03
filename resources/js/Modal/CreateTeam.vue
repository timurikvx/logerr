<script setup>

    import Modal from "@/Components/Modal.vue";
    import {ref} from 'vue'

    // let name = ref('');
    // let url = ref('');

    let form = ref({
        name: '',
        url: ''
    })
    let errors = ref([]);

    function create(){
        axios.post('/team/create', form.value).then(function (response){
            console.log(response.data);
            if(response.data.errors){
                errors.value = response.data.errors
            }else{

            }
        }).catch(function(error){
            //console.error(error.response.data);
        });
    }

</script>

<template>
    <Modal title="Создать команду" class="create-team flex flex-col">
        <div class="grow flex">
            <div class="flex flex-col grow">
                <div>Имя</div>
                <input class="" type="text" v-model="form.name">
            </div>
        </div>
        <div class="grow flex">
            <div class="flex flex-col grow">
                <div>URL</div>
                <input class="" type="text" v-model="form.url">
            </div>
        </div>
        <div class="flex">
            <div class="grow"></div>
            <button class="button" @click="create">Создать</button>
        </div>
    </Modal>
</template>

<style scoped>

</style>
