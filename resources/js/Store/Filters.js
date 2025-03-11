import {defineStore} from "pinia";

export const filtersStore = defineStore('filters', {
    state: () => ({
        show: false,
        errors: []
    }),
    getters: {

    }
})
