<template>
    <Modal title="Новое оповещение" class="create-notification" v-model:visible="modal.newNotification">
        <div class="flex p-0 overflow-hidden">
            <div class="flex flex-col mr-4 w-2/5">
                <div class="flex mb-2">
                    <div class="flex flex-col mb-2 mr-2 w-3/4">
                        <div class="mb-1">Имя</div>
                        <input type="text" class="input" v-model="notification.name">
                    </div>
                    <div class="flex flex-col grow mb-2">
                        <div class="pb-1">Тип данных</div>
                        <div class="flex grow relative">
                            <SelectList :input="false" v-model:value="type" theme="light" :minWidth="100" :list="types" @select="selectType" class="select w-full"></SelectList>
                        </div>
                    </div>
                </div>
                <div class="mb-4 pb-1 text-lg border-bottom text-fuchsia-400">Настройки оповещения</div>
                <div class="flex flex-col mb-2">
                    <div class="pb-1 mb-2 border-bottom">Превышение количества единиц, шт.</div>
                    <input type="number" class="input" placeholder="0" v-model="notification.count">
                    <div class="description">Количество единиц наименований</div>
                </div>
                <div class="flex flex-col mb-2">
                    <div class="pb-1 mb-2 border-bottom">За последние количество минут</div>
                    <input type="number" class="input" placeholder="0" v-model="notification.minutes">
                    <div class="description">Количество минут</div>
                </div>
                <div class="flex flex-col mb-2">
                    <div class="pb-1 mb-2 border-bottom">Минимальное время оповещения, минут</div>
                    <input type="number" class="input" v-model="notification.every">
                    <div class="description">Минимальное время рассылки сообщений</div>
                </div>
            </div>
            <div class="flex flex-col grow overflow-hidden">
                <div class="mb-1">Отслеживаемые поля</div>
                <perfect-scrollbar class="flex flex-col grow">
                    <div class="flex">
                        <div class="add-to-list add" @click="addField()"></div>
                        <div class="grow"></div>
                    </div>
                    <div v-for="(item, index) in fields" class="flex mt-2 mr-4">
                        <SelectList :input="false" v-model:value="item.field" theme="light" placeholder="Поле" :minWidth="300" :list="names" class="select w-full"></SelectList>
                        <input type="text" class="input w-3/5 mr-2" placeholder="Значение">
                        <button class="button red" @click="removeField(index)">Удалить</button>
                    </div>
                </perfect-scrollbar>
            </div>
        </div>
        <div class="flex mt-2">
            <div class="grow"></div>
            <button class="button green" @click="save()">Сохранить</button>
        </div>
    </Modal>
    <ListChoice v-model:visible="choice" :list="list" table="error" column="name" @complete="choiceComplete"></ListChoice>
</template>

<script setup>

    import Modal from "@/Components/Modal.vue";
    import {modalStore} from "@/Store/Modal.js";
    import ListChoice from "@/Components/ListChoice.vue";
    import {ref} from 'vue'
    import SelectList from "@/Components/SelectList.vue";

    const modal = modalStore();
    const types = [
        {'name':'Ошибки', 'value': 'errors'},
        {'name':'Логи', 'value': 'logs'},
    ]

    let choice = ref(false);
    let list = ref([]);
    let field = ref('');
    let fields = ref([
        {'name': '','field': '', 'value': ''},
        {'name': '','field': '', 'value': ''}
    ]);
    let type = ref({});
    let names = ref([
        {'name': 'Удаление данных из очереди'},
        {'name': 'Отправка чека в BI систему'},
        {'name': 'Загрузка заказов в офисе'},
        {'name': 'Проведенеи документов'},
        {'name': 'Ошибка заполнения данных пользователя'},
        {'name': 'Обмен заказами покупателя'},
        {'name': 'Ошибка создания заказа'},
        {'name': 'Сервис недоступен'},
        {'name': 'Загрузка данных из 1С'},
        {'name': 'Ошибка отправки регистра заданий'},
    ]);
    let notification = ref({
        name: '',
        count: 0,
        minutes: 0,
        every: 0
    });

    function choiceComplete(value){
        console.log(value);
    }

    function selectType(value){
        type.value = value;
        getColumns();
    }

    function addField(){
        fields.value.push({'name': '','field': '', 'value': ''});
    }

    function removeField(index){
        fields.value.splice(index, 1);
    }

    function getColumns(){
        axios.post('/notifications/columns', {type: type.value.value}).then(function (response){
            names.value = response.data.columns;
        });
    }

    function save(){
        let data = {
            notification: notification.value,
            type: type.value.value,
            fields: fields.value
        };
        axios.post('/notifications/save', data).then(function (response){

        });
    }

</script>

<style scoped>

</style>
