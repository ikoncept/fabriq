import SidebarItems from '~/routes/sidebar-items'
import * as types from '~/store/mutation-types'

export const state = {
    menuItems: []
}

export const getters = {
    menuItems: state => [...state.menuItems, ...SidebarItems()]
}

export const mutations = {
    [types.SET_SIDEBAR_ITEMS]: (state, data) => {
        const userRoles = window.fabriqCms.userRoles
        // Filter if enabled
        state.menuItems = data.filter(item => {
            return item.enabled
        }).filter(item => {
            // Filter user roles
            if (item.roles.includes('*')) {
                return true
            }
            const matchedRoles = userRoles.filter(element => item.roles.includes(element))
            return matchedRoles.length > 0
        })
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
