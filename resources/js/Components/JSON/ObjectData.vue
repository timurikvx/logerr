<template>
    <div v-for="item in listing" class="json-object mono flex" :class="{'flex-col': (item.type !== '')}">
        <div class="flex">
            <div class="property">{{ item.item }}</div>
            <div>:</div>
            <div class="delimiter ml-2">{{ item.open }}</div>
            <div v-if="item.opening" class="flex">
                <div v-if="!show(item)" @click="open(item)" class="self-center icon plus"></div>
                <div v-else @click="close(item)" class="self-center icon minus"></div>
                <div v-if="!show(item)" class="delimiter">{{ item.close }}</div>
            </div>
        </div>
        <div v-if="show(item)" :class="{'indent': (item.type !== '')}">
            <DataPrint :data="item.value"></DataPrint>
        </div>
        <div v-if="show(item)" class="delimiter">{{ item.close }}</div>
    </div>
</template>

<script setup>

    import DataPrint from "@/Components/DataPrint.vue";
    import {computed, defineProps, ref} from 'vue';

    const props = defineProps({
        data: Object
    });

    let keys = ref([]);

    function show(item){
        if(!item.opening){
            return true;
        }
        return keys.value.includes(item.item);
    }

    function open(item){
        keys.value.push(item.item);
    }

    function close(item){
        let i = keys.value.indexOf(item.item);
        if(i >= 0){
            keys.value.splice(i, 1);
        }
    }

    let listing = computed({
        get(){
            let arr_keys = Object.keys(props.data);
            let arr = [];
            for (let i of arr_keys){
                let value = props.data[i];
                let type  = typing(value);
                arr.push({
                    value: value,
                    item: i,
                    type: type,
                    open: getDelimiter(type),
                    close: getDelimiter(type, true),
                    opening: getDelimiter(type) !== '',
                    opened: false
                });
            }
            return arr;
        }
    })

    function typing(value){
        if(Array.isArray(value)){
            return 'array';
        }else if(value instanceof Object){
            return 'object';
        }
        return '';
    }

    function getDelimiter(type, close){
        if(type === 'array'){
            return (close)? ']': '[';
        }else if(type === 'object'){
            return (close)? '}': '{';
        }else{
            return '';
        }
    }

</script>

<style scoped>

</style>
