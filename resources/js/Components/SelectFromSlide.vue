<template>
    <div ref="parent" class="select-from-slide">
        <div class="list absolute z-10" ref="slide" :class="[{'visibility': show}, {'invisible': !show}, {'active': show}]">
            <div v-for="item in list" class="flex item cursor-pointer" @click="select(item)">
                <div class="text-center grow p-2">{{ item.name }}</div>
            </div>
        </div>
        <div :id="'item-' + id" @click="show_select()">
            <slot></slot>
        </div>
    </div>
</template>

<script setup>

    import {defineProps, ref, defineEmits, onMounted} from 'vue'

    const slide = ref(null);
    const parent = ref(null);
    const id = Math.random() * 1000000000000000000;
    const props = defineProps({
        list: Array,
        name: String
    });
    const emits = defineEmits(['change']);

    let top = ref(0);
    let width = ref(0);
    let show = ref(false);
    let into = ref(false);

    onMounted(()=>{
        document.addEventListener('click', click);
        slide.value.addEventListener('mouseover', mouseover);
        slide.value.addEventListener('mouseleave', mouseleave);
    })

    function show_select(){
        show.value = true;
        let element = document.querySelector('.select-from-slide #item-' + id);
        const rect = element.getBoundingClientRect();
        const list = slide.value.getBoundingClientRect();
        top.value = ((rect.y - (list.height / 2)) - (rect.height / 2));
    }

    function select(item){
        show.value = false;
        emits('change', {name: props.name, filter: item});
    }

    function mouseover(e){
        into.value = true;
    }

    function mouseleave(e){
        into.value = false;
    }

    function click(e){
        if(!show.value){
            return;
        }
        let element = document.querySelector('#item-' + id);
        if(element === e.target.closest('#item-' + id)){
            return;
        }
        let p = e.target.closest('.select-from-slide .list');
        if(p !== null){
            console.log('return');
            return;
        }
        if(!into.value){
            show.value = false;
        }
    }

</script>


<style scoped>

    .select-from-slide .list{
        background-color: var(--main);
        border: 1px solid var(--border);
        transition: 0.2s;
        transform: translateY(-40%) scale3d(1, 0.1, 2);
        width: 180px;
        box-shadow: 0px 1px 10px 0px rgb(0 0 0 / 89%);
        border-radius: 6px;
    }

    .select-from-slide .list.active{
        transform: translateY(-40%) scale3d(1, 1, 1);
    }

    .select-from-slide{
        width: 100%;
    }

    .select-from-slide .item{

    }

</style>
