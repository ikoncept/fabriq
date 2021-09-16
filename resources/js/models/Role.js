import axios from 'axios'

export default {
    endpoint: '/api/admin/roles/',

    async index (payload) {
        const { data } = await axios.get(this.endpoint, payload)

        return data
    }

}
