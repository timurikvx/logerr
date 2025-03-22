import {defineStore} from "pinia";

export const inviteStore = defineStore('invite', {
    state: () => ({
        show: false,
        team: {name: 'casasdsad'}
    }),
    getters: {

    }
})
