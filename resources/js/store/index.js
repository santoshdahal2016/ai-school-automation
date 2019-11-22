import Vue from 'vue'
import Vuex from 'vuex'
import getters from './getters'
import user from './modules/user'
import profile from "./modules/profile";
import candidate from "./modules/candidate";
import app from './modules/app'

Vue.use(Vuex)

export const store = new Vuex.Store({
    modules: {
        user,
        profile,
        app,
        candidate
    },
    state: {

        loading: false,
        serverUrl: 'http://naamii.test',
    },
    mutations: {

        setLoading(state, payload) {
            state.loading = payload
        },

    },
    actions: {


    },
    getters
})
