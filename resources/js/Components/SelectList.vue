<template>
    <div class="select-list flex flex-col relative">
        <input v-if="input" ref="item" type="text" :placeholder="placeholder" :style="getInputStyle()" :value="value.name" class="value input grow cursor-pointer">
        <div v-else ref="item" class="value input p-2 grow content-center cursor-pointer" :style="getInputStyle()">
            <div v-if="value?.name">{{ value.name }}</div>
            <div v-else>{{ placeholder }}</div>
        </div>
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
        },
        placeholder: {
            type: String,
            default: ''
        }
    });
    const emits = defineEmits(['update:value', 'select']);
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
        let element = e.target.closest('.value.input');
        if(item.value === element){
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
        value.value = item;
        emits('select', item);
    }

    function getInputStyle(){
        return 'min-width: ' + props.minWidth + 'px';
    }

</script>

<style scoped>

</style>
