import * as types from '@/store/mutation-types'

export const state = {
    menuOpen: false,
    openCards: [],
}

export const getters = {
    menuOpen: state => state.menuOpen,
    openCards: state => state.openCards,
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
    },
    'toggleOpenCard': (state, identifier) => {
        const index = state.openCards.indexOf(identifier);

        if (index === -1) {
            state.openCards.push(identifier);
        } else {
            state.openCards.splice(index, 1);
        }
    },
}

export const actions = {}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
}
