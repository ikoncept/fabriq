import axios from 'axios'

export default {
    endpoint: '/api/user/notifications/',

    async index (payload) {
        const { data } = await axios.get(this.endpoint, payload)

        return data
    },

    async update (id, payload) {
        const { data } = await axios.patch(this.endpoint + id, payload)

        return data
    }

}
