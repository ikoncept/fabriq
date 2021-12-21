import * as types from '~/store/mutation-types'

export const state = {
    usersIdle: {}
}

export const getters = {
    usersIdle: state => state.usersIdle
}

export const mutations = {
    [types.USER_JOINING]: (state, data) => {
        // state.menuOpen = !state.menuOpen
        if (!state.usersIdle[data.identifier]) {
            return
        }
        state.usersIdle[data.identifier].findIndex(item => item.id === data.user.id) > -1 ? console.log('dupe') : state.usersIdle[data.identifier].push(data.user)
    },
    [types.USER_LEAVING]: (state, data) => {
        if (!state.usersIdle[data.identifier]) {
            return
        }
        const index = state.usersIdle[data.identifier].findIndex(item => item.id === data.user.id)
        if (index > -1) {
            state.usersIdle[data.identifier].splice(index, 1)
        }
    },
    [types.SET_USERS_HERE]: (state, data) => {
        state.usersIdle = data
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
