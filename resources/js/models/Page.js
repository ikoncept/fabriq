import axios from 'axios'

export default {
    endpoint: '/api/admin/pages/',

    async index (payload) {
        const { data } = await axios.get(this.endpoint, payload)

        return data
    },

    async count (payload) {
        const { data } = await axios.get(this.endpoint + 'count', payload)

        return data
    },

    async store (payload) {
        const { data } = await axios.post(this.endpoint, payload)

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

    async destroy (id) {
        const { data } = await axios.delete(this.endpoint + id)

        return data
    },

    async publish (id) {
        const { data } = await axios.post(this.endpoint + id + '/publish')

        return data
    },

    async signedPreview (id) {
        const { data } = await axios.get(this.endpoint + id + '/signed-url')

        return data
    },

    async paths (id, payload) {
        const { data } = await axios.get(this.endpoint + id + '/paths', payload)

        return data
    },

    async clone (id, payload) {
        const { data } = await axios.post(this.endpoint + id + '/clone', payload)

        return data
    }
}
