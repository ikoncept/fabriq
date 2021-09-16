import axios from 'axios'

export default {
    endpoint: '/api/admin/menu-items/',

    async show (id, object) {
        const { data } = await axios.get(this.endpoint + id, object)

        return data
    },

    async update (id, object) {
        const { data } = await axios.patch(this.endpoint + id, object)

        return data
    },

    async store (id, object) {
        const { data } = await axios.post('/api/admin/menus/' + id + '/items', object)

        return data
    },

    async destroy (id) {
        const { data } = await axios.delete('/api/admin/menu-items/' + id)

        return data
    }

}
