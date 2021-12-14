import Cookies from 'js-cookie'
import Echo from 'laravel-echo'
import 'pusher-js'
import Vue from 'vue'

const echo = {
    install (Vue, options) {
        Vue.prototype.$echo = new Echo({
            broadcaster: 'pusher',
            key: window.fabriqCms.pusher.key,
            wsHost: 'ws.ikoncept.io',
            wsPort: 6001,
            enabledTransports: ['ws', 'wss'],
            forceTLS: false,
            auth: {
                withCredentials: true,
                headers: {
                    'X-XSRF-TOKEN': Cookies.get('XSRF-TOKEN')
                }
            }
        })
    }
}
Vue.use(echo)
