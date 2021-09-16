import axios from 'axios'

export default {
    endpoint: '/api/admin/menus/',

    async show (id, payload) {
        const { data } = await axios.get(this.endpoint + id, payload)

        return data
    },

    async showTree (id, payload) {
        const { data } = await axios.get(this.endpoint + id + '/items/tree', payload)

        return data
    },

    async updateTree (id, payload) {
        const { data } = await axios.patch(this.endpoint + id + '/items/tree', payload)

        return data
    },

    async index (payload) {
        const { data } = await axios.get(this.endpoint, payload)

        return data
    },

    async store (id, payload) {
        const { data } = await axios.get(this.endpoint + id, payload)

        return data
    }
}
