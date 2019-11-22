import axios from 'axios';

export default {
    state: {
        candidates: [],
        candidate: {},
    },
    mutations: {
        setCandidate(state, payload) {
            state.candidate = payload
        },
        setCandidates(state, payload) {
            state.candidates = payload;
        },
        setCandidateId(state, payload) {
            state.candidate.id = payload
        },

    },
    actions: {
        fetchCandidates({commit, state, rootState}, payload) {
            return new Promise(((resolve, reject) => {
                commit('setLoading', true);
                axios.get('/candidates', {params: payload})
                    .then(response => {
                        commit('setLoading', false)
                        commit('setCandidates', response.data.data)
                        resolve(response)

                    })
                    .catch(error => {
                        commit('setLoading', false);
                        reject(error)
                    })

            }))

        },

        sendEmail({commit, state, rootState}, payload) {
            return new Promise(((resolve, reject) => {
                commit('setLoading', true);
                axios.get('/candidates/send_email/' + payload.id)
                    .then(response => {
                        commit('setLoading', false)
                        resolve(response)
                    })
                    .catch(error => {
                        commit('setLoading', false);
                        reject(error)
                    })

            }))

        },
        csvLoad({commit, state, rootState}, payload) {
            return new Promise(((resolve, reject) => {
                commit('setLoading', true);
                axios.post('/candidates/import_csv' , payload)
                    .then(response => {
                        commit('setLoading', false)
                        resolve(response)
                    })
                    .catch(error => {
                        commit('setLoading', false);
                        reject(error)
                    })

            }))

        },
        setCandidate({commit, state}, payload) {
            commit('setCandidate', payload);
        },
        clearCandidate({commit}) {
            commit('setCandidate', {})
        },

    },
    getters: {
        getCandidate: state => {
            return state.candidate;
        },
        getCandidates: state => {
            return state.candidates;
        }
    }
}
