import axios from 'axios'

export default {
    endpoint: '/api/admin/images/',

    async show (id, payload) {
        const { data } = await axios.get(this.endpoint + id, payload)

        return data
    },

    async index (payload) {
        const { data } = await axios.get(this.endpoint, payload)

        return data
    },

    async count (payload) {
        const { data } = await axios.get(this.endpoint + 'count', payload)

        return data
    },

    async update (id, payload) {
        const { data } = await axios.patch(this.endpoint + id, payload)

        return data
    },

    async destroy (id) {
        const { data } = await axios.delete(this.endpoint + id)

        return data
    },

    async attachToModel (id, model, payload) {
        const { data } = await axios.post(this.endpoint + id + '/' + model, payload)

        return data
    },

    async relatedIndex (id, model, payload = {}) {
        const { data } = await axios.get('/api/admin/' + model + '/' + id + '/images', payload)

        return data
    },

    async srcSet (id, payload = {}) {
        const { data } = await axios.get('/api/images/' + id + '/src-set', payload)

        return data
    }
}
