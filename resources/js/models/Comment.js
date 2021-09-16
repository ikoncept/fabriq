import axios from 'axios'

export default {
    endpoint: '/api/admin/',

    async index (modelName, modelId, payload) {
        const { data } = await axios.get(this.endpoint + modelName + '/' + modelId + '/comments', payload)

        return data
    },

    async store (modelName, modelId, payload) {
        const { data } = await axios.post(this.endpoint + modelName + '/' + modelId + '/comments', payload)

        return data
    },

    async show (id, payload) {
        const { data } = await axios.get(this.endpoint + id, payload)

        return data
    },

    async update (id, payload) {
        const { data } = await axios.patch(this.endpoint + id, payload)

        return data
    },

    async destroy (id, payload) {
        const { data } = await axios.delete(this.endpoint + id, payload)

        return data
    }
}
