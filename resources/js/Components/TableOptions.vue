<template>
    <Modal title="Настройка таблицы" class="table-options" v-model:visible="store.show">
        <div class="flex flex-col grow">
                <div v-for="column in columns" class="flex mb-2">
                    <div class="column flex grow mr-2 cursor-pointer">
                        <div class="capture mr-4"></div>
                        <div class="mr-4 grow">{{ column.name }}</div>
                    </div>
                    <div class="flex mr-4">
                        <button v-if="column.hidden" class="visible column-hidden" @click="toggleShowColumns(column, false)">Скрыто</button>
                        <button v-else class="visible" @click="toggleShowColumns(column, true)">Скрыть</button>
                    </div>
                    <div class="flex">
                        <div class="self-center mr-4">Ширина</div>
                        <div class="mr-2">
                            <Number class="width" :min="0" v-model:value="column.width"></Number>
<!--                            <input style="width: 65px" class="input" type="number" v-model="column.width" placeholder="0">-->
                        </div>
                        <div class="self-center icon return" title="Сбросить" @click="resetColumn(column)"></div>
                    </div>
                </div>
        </div>
    </Modal>
</template>

<script setup>

    import Modal from "@/Components/Modal.vue";
    import { columnsStore } from '@/Store/Columns.js';
    import {defineProps, defineEmits, onMounted, ref} from 'vue'
    import Number from "@/Components/Number.vue";

    const store = columnsStore();
    const props = defineProps({
        columns: Array
    });
    const emits = defineEmits(['update:columns']);

    let column = ref(null);

    onMounted(()=>{
        let elements = document.querySelectorAll('.table-options .column');
        console.log('elements', elements);
    });

    function toggleShowColumns(column, value){
        column.hidden = value;
    }

    function resetColumn(column){
        column.width = 0;
    }

</script>

<style scoped>

</style>
