import {defineStore} from "pinia";
import {computed, ref} from "vue";

export const notificationsStore = defineStore('notifications', () => {

    const list = ref([]);
    const exist = computed(() => list.value.length > 0 );

    function get(){
        axios.post('/notifications/get').then(function (response){
            if(response.data.list){
                list.value = response.data.list;
            }
        })
    }

    function intervalGet(time){
        time = time || 60000;
        setInterval(get, time);
    }

    return {
        list,
        exist,
        get,
        intervalGet
    }

})
