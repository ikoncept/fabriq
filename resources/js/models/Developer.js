import axios from 'axios'

export default {
    endpoint: '/api/dev/',

    async bustCache (payload) {
        const { data } = await axios.post(this.endpoint + 'bust-cache', payload)

        return data
    }

}
