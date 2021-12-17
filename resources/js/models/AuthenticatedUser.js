import axios from 'axios'

export default {
    endpoint: '/api/user/',

    async index (payload) {
        const { data } = await axios.get(this.endpoint, payload)

        return data
    },

    async update (object) {
        const { data } = await axios.patch(this.endpoint, object)

        return data
    },

    async sendVerificationRequest () {
        const { data } = await axios.post(this.endpoint + 'send-email-verification')

        return data
    },

    async deleteImage () {
        const { data } = await axios.delete(this.endpoint + 'image')
        return data
    }
}
