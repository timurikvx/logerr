<template>
    <div class="font-bold text-2xl mb-4" v-if="props.edit">Изменение оповещения</div>
    <div class="font-bold text-2xl mb-4" v-else>Создание оповещения</div>
    <div class="flex p-0 overflow-hidden grow create-notification">
        <div class="flex flex-col mr-4 w-2/5">
            <div class="flex mb-2">
                <div class="flex flex-col mb-2 mr-2 w-3/4">
                    <div class="mb-1">Имя</div>
                    <input type="text" class="input" v-model="notification.name">
                </div>
                <div class="flex flex-col grow mb-2">
                    <div class="pb-1">Тип данных</div>
                    <div class="flex grow relative">
                        <SelectList :input="false" v-model:value="notification.type" theme="light" :minWidth="100" :list="types" @select="selectType" class="select w-full"></SelectList>
                    </div>
                </div>
            </div>
            <div class="mb-4 pb-1 text-lg border-bottom text-fuchsia-400">Настройки оповещения</div>
            <div class="flex flex-col mb-2">
                <div class="mb-2">Чат телеграм</div>
                <SelectList :input="false" v-model:value="notification.chat" theme="light" placeholder="Выберите чат" :minWidth="200" :list="chats" class="select w-full"></SelectList>
            </div>
            <div class="flex flex-col mb-2">
                <div class="mb-2">Превышение количества единиц, шт.</div>
                <input type="number" class="input" placeholder="0" v-model="notification.count">
                <div class="description">Если количество значений поля превышает это количество</div>
            </div>
            <div class="flex flex-col mb-2">
                <div class="mb-2">За последние количество минут</div>
                <input type="number" class="input" placeholder="0" v-model="notification.minutes">
                <div class="description">Время за которое отслеживается превышение значений поля</div>
            </div>
            <div class="flex flex-col mb-2">
                <div class="mb-2">Минимальное время оповещения, минут</div>
                <input type="number" class="input" v-model="notification.every">
                <div class="description">Минимальное время рассылки сообщений</div>
            </div>
        </div>
        <div class="flex flex-col grow overflow-hidden">
            <div class="mb-1">Отслеживаемые поля</div>
            <perfect-scrollbar class="flex flex-col grow" v-if="notification.type?.value">
                <div class="flex">
                    <div class="add-to-list add" @click="addField()"></div>
                    <div class="grow"></div>
                </div>
                <div v-for="(item, index) in fields" class="flex mt-2 mr-4">
                    <SelectList :input="false" v-model:value="item.field" theme="light" placeholder="Поле" :minWidth="300" :list="columns" class="select w-full"></SelectList>
                    <input type="text" class="input w-3/5 mr-2" placeholder="Значение" v-model="item.value">
                    <button class="button red" @click="removeField(index)">Удалить</button>
                </div>
            </perfect-scrollbar>
            <div v-else class="flex grow">
                <div class="m-auto text-2xl">Выберите тип</div>
            </div>
        </div>
    </div>
    <div class="flex mt-2">
        <div class="grow"></div>
        <button class="button green" @click="save()">Сохранить</button>
    </div>
    <ListChoice v-model:visible="choice" :list="list" table="error" column="name"></ListChoice>
</template>

<script setup>

    import {modalStore} from "@/Store/Modal.js";
    import ListChoice from "@/Components/ListChoice.vue";
    import {computed, ref} from 'vue'
    import SelectList from "@/Components/SelectList.vue";

    const props = defineProps({
        chats: Array,
        edit: {
            type: Boolean,
            default: false
        },
        notification: {
            type: Object,
            default: null
        },
        fields:{
            type: Array,
            default: null
        },
        columns: {
            type: Array,
            default: null
        }
    })
    const emits = defineEmits(['update:notification', 'update:fields']);
    const modal = modalStore();
    const types = [
        {'name':'Ошибки', 'value': 'errors'},
        {'name':'Логи', 'value': 'logs'},
    ]

    let choice = ref(false);
    let list = ref([]);
    let field = ref('');
    let _fields = ref([]);
    let _columns = ref([]);
    let _notification = ref({
        name: '',
        chat: {},
        type: {},
        count: 0,
        minutes: 0,
        every: 0
    });
    let notification = computed({
        get(){
            if(props.notification){
                return props.notification;
            }else{
                return _notification.value;
            }
        },
        set(value){
            _notification.value = value;
            emits('update:notification', value);
        }
    });
    let fields = computed({
        get(){
            if(props.fields){
                return props.fields;
            }else{
                return _fields.value;
            }
        },
        set(value){
            console.log('_fields');
            _fields.value = value;
            emits('update:fields', value);
        }
    });
    let columns = computed({
        get(){
            if(props.columns){
                return props.columns;
            }else{
                return _columns.value;
            }
        },
        set(value){
            _columns.value = value;
        }
    });

    function selectType(value){
        notification.value.type = value;
        getColumns();
    }

    function addField(){
        fields.value.push({'name': '','field': '', 'value': ''});
    }

    function removeField(index){
        fields.value.splice(index, 1);
    }

    function getColumns(){
        axios.post('/notifications/columns', {type: notification.value.type?.guid}).then(function (response){
            columns.value = response.data.columns;
        });
    }

    function save(){
        let data = {
            notification: notification.value,
            fields: fields.value
        };
        axios.post('/notifications/save', data).then(function (response){

        });
    }

</script>

<style scoped>

</style>
