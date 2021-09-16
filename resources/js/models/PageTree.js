import axios from 'axios'

export default {
    endpoint: '/api/admin/pages-tree/',

    async index (payload) {
        const { data } = await axios.get(this.endpoint, payload)

        return data
    },

    async update (payload) {
        const { data } = await axios.patch(this.endpoint, payload)

        return data
    }
}
