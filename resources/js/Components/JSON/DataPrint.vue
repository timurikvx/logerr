<template>
    <div class="flex json-object">
        <div v-if="type === 'array'">
            <ObjectData :data="data"></ObjectData>
        </div>
        <div v-if="type === 'object'">
            <ObjectData :data="data"></ObjectData>
        </div>
        <div v-if="type === 'string'" class="flex">
            <div class="string">"{{ data }}"</div>
            <div class="ml-3 text-gray-500">Строка</div>
        </div>
        <div v-if="type === 'boolean'" class="flex">
            <div class="boolean">{{ data }}</div>
            <div class="ml-3 text-gray-500">Булево</div>
        </div>
        <div v-if="type === 'number'" class="flex">
            <div class="number">{{ data }}</div>
            <div class="ml-3 text-gray-500">Число</div>
        </div>
    </div>
</template>

<script setup>

    import ObjectData from "@/Components/JSON/ObjectData.vue";
    import {defineProps, computed} from 'vue'

    const props = defineProps({
        data: [Array, Object, String, Number, Boolean]
    });

    let type = computed({
        get(){
            if(!props.data){
                return 'null';
            }else if(Array.isArray(props.data)){
                return 'array';
            }else if(props.data instanceof Object){
                return 'object';
            }else if(typeof props.data === "boolean"){
                return 'boolean';
            }else if(!isNaN(Number(props.data))){
                return 'number';
            }else{
                return 'string';
            }
        }
    });

</script>

<style scoped>

</style>
