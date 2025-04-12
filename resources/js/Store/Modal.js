import {defineStore} from "pinia";
import {ref} from 'vue';

export const modalStore = defineStore('modal', () => {

    const filters = ref(false);
    const sort = ref(false);
    const createTeams = ref(false);
    const columns = ref(false);
    const setName = ref(false);
    const notifications = ref(false);
    const invite = ref(false);
    const telegramChat = ref(false);
    //errors
    const errors = ref(false);
    const error = ref('');
    //notice
    const notices = ref(false);
    const notice = ref('');

    function setError(text){
        if(text.length > 0){
            errors.value = true;
        }
        error.value = text;
    }

    function setNotice(text){
        if(text.length > 0){
            notices.value = true;
        }
        notice.value = text;
    }

    return {
        filters,
        sort,
        createTeams,
        columns,
        setName,
        notifications,
        invite,
        errors,
        error,
        notice,
        telegramChat,
        //functions
        setError,
        setNotice
    }
})

// export const modalStore = defineStore('modal', {
//     state: () => ({
//         filters: false,
//         sort: false,
//         createTeams: false,
//         columns: false,
//         setName: false,
//         notifications: false,
//         invite: false,
//         errors: false
//     }),
//     getters: {
//
//     }
// })
