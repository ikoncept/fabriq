import * as types from '@/store/mutation-types'

export const state = {
    page: {
        id: 0,
        updated_at: '2020-01-01 10:00:00',
        localizedContent: {
            sv: {},
        },

        template: {
            data: {},
        },
    },
}

export const getters = {
    page: state => state.page,
}

export const mutations = {
    [types.SET_PAGE]: (state, data) => {
        state.page = data
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
