import * as types from '~/store/mutation-types'

export const state = {
    menuOpen: false
}

export const getters = {
    menuOpen: state => state.menuOpen
}

export const mutations = {
    [types.TOGGLE_MENU]: (state) => {
        state.menuOpen = !state.menuOpen
    },
    [types.OPEN_MENU]: (state) => {
        state.menuOpen = true
    },
    [types.CLOSE_MENU]: (state) => {
        state.menuOpen = false
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
