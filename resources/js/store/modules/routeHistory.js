import * as types from '../mutation-types'

export const state = {
    lastRoute: null
}

export const getters = {
    lastRoute: state => state.lastRoute
}

export const mutations = {
    [types.SET_FROM_ROUTE]: (state, data) => {
        state.lastRoute = data
    }
}

export const actions = {}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}
