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
    const errors = ref(false);

    const error = ref('');

    function setError(text){
        if(text.length > 0){
            errors.value = true;
        }
        error.value = text
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
        //functions
        setError
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
