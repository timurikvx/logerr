import {defineStore} from "pinia";

export const columnsStore = defineStore('columns', {
    state: () => ({
        show: false,
        errors: []
    }),
    getters: {

    }
});
