import axios from 'axios'

export default {
    endpoint: '/api/admin/videos/',

    async show (id, payload) {
        const { data } = await axios.get(this.endpoint + id, payload)

        return data
    },

    async index (payload) {
        const { data } = await axios.get(this.endpoint, payload)

        return data
    },

    async update (id, payload) {
        const { data } = await axios.patch(this.endpoint + id, payload)

        return data
    },

    async destroy (id) {
        const { data } = await axios.delete(this.endpoint + id)

        return data
    }
}
