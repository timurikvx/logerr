import {defineStore} from "pinia";

export const useShitStore = defineStore('shit', {
    state: () => ({
        show: false
    })
})
