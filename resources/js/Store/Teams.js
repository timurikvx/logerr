import {defineStore} from "pinia";

export const modalStore = defineStore('teams', {
    state: () => ({
        createTeams: false,
        list: []
    }),
    getters: {

    }
})
