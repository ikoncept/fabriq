import axios from 'axios'

export default {
    endpoint: '/api/admin/users/',

    async index (payload) {
        const { data } = await axios.get(this.endpoint, payload)

        return data
    },

    async show (id, payload) {
        const { data } = await axios.get(this.endpoint + id, payload)

        return data
    },

    async update (id, object) {
        const { data } = await axios.patch(this.endpoint + id, object)

        return data
    },

    async store (object) {
        const { data } = await axios.post(this.endpoint, object)

        return data
    },

    async destroy (id) {
        const { data } = await axios.delete(this.endpoint + id)

        return data
    }

}
