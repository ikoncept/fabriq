import SidebarItems from '~/config/sidebar-items'

export const state = {
    menuItems: SidebarItems()
}

export const getters = {
    menuItems: state => state.menuItems
}

export const mutations = {}

export const actions = {}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}
