import AuthenticatedUser from '~/models/AuthenticatedUser'
import Notification from '~/models/Notification'
import * as types from '~/store/mutation-types'

export const state = {
    user: {
        id: 0,
        email: '',
        created_at: '',
        updated_at: '',
        role_list: []
    },
    notifications: []
}

export const getters = {
    user: state => state.user,
    notifications: state => state.notifications,
    roles: (getters) => {
        return getters.user.role_list
    },
    isDev: (getters) => {
        return getters.user.role_list.includes('dev')
    }
}

export const mutations = {
    [types.SET_USER]: (state, data) => {
        state.user = data
    },
    [types.SET_NOTIFICATIONS]: (state, data) => {
        state.notifications = data
    }
}

export const actions = {
    async index ({ commit }) {
        const { data } = await AuthenticatedUser.index()
        commit(types.SET_USER, data)
    },
    async notifications ({ commit }) {
        try {
            const payload = {
                params: {
                    'filter[unseen]': true,
                    number: 300,
                    field: 'id'
                }
            }
            const { data } = await Notification.index(payload)
            commit(types.SET_NOTIFICATIONS, data)
        } catch (error) {
            console.error(error)
        }
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}
