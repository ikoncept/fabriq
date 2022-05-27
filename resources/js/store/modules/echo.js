import * as types from '~/store/mutation-types';

export const state = {
    usersIdle: {}
}

export const getters = {
    usersIdle: (state) => {
        const localCopy = { ...state.usersIdle }
        Object.keys(localCopy).forEach((key, index) => {
            localCopy[key].sort((a, b) => {
                return a.timestamp - b.timestamp
            })
        })

        return localCopy
    },
    currentUserIsFirstIn: (state, getters, rootState, rootGetters) => {
        const users = Object.values(getters.usersIdle)[0]

        if (!users) {
            return true
        }
        if (users.length <= 1) {
            return true
        }

        if (users[0].id === rootGetters['user/user'].id) {
            return true
        }

        return false
    }
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
