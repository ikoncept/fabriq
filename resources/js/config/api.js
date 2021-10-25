import axios from 'axios'

export default function setup (vm) {
    axios.interceptors.request.use(request => {
        request.headers['X-LOCALE'] = vm.$store.getters['config/activeLocale']
        // Important: request interceptors **must** return the request.
        return request
    })

    axios.interceptors.response.use(response => {
        return response
    }, error => {
        if (error.response) {
            const { status, data } = error.response
            if (status >= 500) {
                console.error('Serverfel - ' + error.response.status)
                vm.$toast.error({
                    title: `Serverfel - ${status}`,
                    message: `${data.message}`
                })
            }
            if (status === 422) {
                vm.$toast.warning({ title: 'Oj då!', message: 'Något är tosigt med datan du försöker skicka.' })
            }
            if (status === 400) {
                vm.$toast.info({ title: 'Pst!', message: data.error.message })
            }
            if (status === 404) {
                vm.$toast.warning({ title: 'Hmm!', message: 'Kunde inte hitta det du letade efter' })
            }
            if (status === 405) {
                vm.$toast.warning({ title: 'Oj!', message: 'Det var ingen tillåten handling' })
            }
            if (status === 403) {
                vm.$toast.error({ title: 'Stopp!', message: 'Du saknar behörighet för att göra detta.' })
            }
        }
        return Promise.reject(error)
    })

    axios.create({
        withCredentials: true
    })
}
