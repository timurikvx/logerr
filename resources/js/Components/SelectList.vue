<template>
    <div class="select-list flex flex-col relative">
        <input v-if="input" ref="item" type="text" :placeholder="placeholder" :style="getInputStyle()" :value="value.name" :title="value.name" class="value grow cursor-pointer">
        <div v-else ref="item" class="value p-2 grow content-center cursor-pointer" :class="{'light': theme === 'light'}" :style="getInputStyle()">
            <div v-if="value?.name" :title="value.name">{{ value.name }}</div>
            <div v-else class="placeholder">{{ placeholder }}</div>
        </div>
        <div class="fixed listing z-10 flex flex-col overflow-hidden" v-if="list.length > 0" :style="style" :key="key" v-show="list_visible"  @mouseleave="mouseleave" @mouseover="mouseover">
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
        },
        theme: {
            type: String,
            default: 'dark'
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
        let element = e.target.closest('.value');
        if(item.value === element){
            list_visible.value = !list_visible.value;
            show();
            return;
        }
        if(!into.value){
            list_visible.value = false;
        }
    }

    function show(){
        if(!item.value){
            return;
        }
        const rect = item.value.getBoundingClientRect();
        let top = rect.top + 44;//rect.height;
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
