import axios from 'axios'
import * as types from '~/store/mutation-types'

export const state = {
    config: {
        modules: [],
        supported_locales: {
            sv: {
                name: 'Swedish',
                script: 'Latn',
                native: 'Svenska',
                regional: 'sv_SE'
            }
        }
    },
    activeLocale: 'sv',
    devMode: false
}

export const getters = {
    config: state => state.config,
    supportedLocales: (state, getters) => {
        return getters.config.supported_locales
    },
    activeLocale: state => state.activeLocale,
    devMode: state => state.devMode
}

export const mutations = {
    [types.SET_CONFIG]: (state, data) => {
        state.config = data
    },
    [types.SET_ACTIVE_LOCALE]: (state, data) => {
        state.activeLocale = data
    },
    [types.SET_DEV_MODE]: (state, data) => {
        state.devMode = data
    }
}

export const actions = {
    async index ({ commit }) {
        const { data } = await axios.get('/api/config')
        commit(types.SET_CONFIG, data.data)
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}
