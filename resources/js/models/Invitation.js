import axios from 'axios'

export default {
    endpoint: '/api/admin/invitations/',

    async show(id, payload) {
        const { data } = await axios.get(this.endpoint + id, payload)

        return data
    },

    async store (id, payload) {
        const { data } = await axios.post(this.endpoint  + id, payload)

        return data
    },

    async destroy (id, payload) {
        const { data } = await axios.delete(this.endpoint + id, payload)

        return data
    }

}
