import axios from 'axios'

export default {
    endpoint: '/api/admin/tags/',

    async index (payload) {
        const { data } = await axios.get(this.endpoint, payload)

        return data
    },

    async store (payload) {
        const { data } = await axios.post(this.endpoint, payload)

        return data
    }
}
