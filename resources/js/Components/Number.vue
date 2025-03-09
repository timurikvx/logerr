<template>
    <input :key="key" type="number" class="input" :class="getClass()" v-model="val" placeholder="0">
</template>

<script setup>

    import {defineProps, defineEmits, computed, ref} from 'vue'

    const emits = defineEmits(['update:value']);
    const props = defineProps({
        class: {
            type: String,
            default: ''
        },
        min: {
            type: Number,
            default: 0
        },
        max: {
            type: Number,
            default: 9999999
        },
        value: {
            type: Number,
            default: 0
        }
    });
    let key = ref(Math.random());

    let val = computed({
        get(){
            return props.value;
        },
        set(value){
            if(value < props.min){
                value = props.min;
            }
            if(value > props.max){
                value = props.max;
            }
            emits('update:value', value);
            key.value = Math.random();
        }
    });

    function getClass(){
        return props.class;
    }

</script>

<style scoped>

</style>
