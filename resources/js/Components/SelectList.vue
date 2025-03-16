<template>
    <div class="select-list flex flex-col relative">
        <input v-if="input" ref="item" type="text" placeholder="" :style="getInputStyle()" :value="value.name" class="input grow cursor-pointer">
        <div v-else ref="item" class="input p-2 grow content-center cursor-pointer" :style="getInputStyle()">{{ value.name }}</div>
        <div class="fixed listing z-10 flex flex-col overflow-hidden" :style="style" :key="key" v-show="list_visible"  @mouseleave="mouseleave" @mouseover="mouseover">
            <PerfectScrollbar>
                <div v-for="item in list" class="p-2 item cursor-pointer" @click="select(item)">
                    <div>{{ item.name }}</div>
                </div>
            </PerfectScrollbar>
        </div>
    </div>
</template>

<script setup>

import {ref, defineProps, onMounted, defineEmits, computed} from "vue";

    const props = defineProps({
        input: Boolean,
        value: Object,
        list: Array,
        minWidth:{
            type: Number,
            default: 0
        }
    });
    const emits = defineEmits(['update:value'])
    const item = ref(null);

    let style = ref('');
    let list_visible = ref(false);
    let key = ref(Math.random());
    let into = ref(false);
    let value = computed({
        get(){
            return props.value;
        },
        set(val){
            emits('update:value', val);
        }
    })

    onMounted(()=>{
        document.addEventListener('click', click);
        show();
    })

    function mouseleave(){
        into.value = false;
    }

    function mouseover(){
        into.value = true;
    }

    function click(e){
        if(item.value === e.target){
            list_visible.value = !list_visible.value;
            return;
        }
        if(!into.value){
            list_visible.value = false;
        }
    }

    function show(){
        const rect = item.value.getBoundingClientRect();
        let top = rect.top + rect.height;
        style.value = 'width: ' + rect.width + 'px; top: ' + top + 'px';
    }

    function select(item){
        key.value = Math.random();
        list_visible.value = false;
        setTimeout(show, 20);
        console.log('item', item);
        value.value = item;
        console.log('value.value', value.value);
    }

    function getInputStyle(){
        return 'min-width: ' + props.minWidth + 'px';
    }

</script>

<style scoped>

</style>
