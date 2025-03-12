import {defineStore} from "pinia";

export const modalStore = defineStore('modal', {
    state: () => ({
        filters: false,
        sort: false,
        createTeams: false,
        columns: false
    }),
    getters: {

    }
})
