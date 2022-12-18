import Vue from 'vue'

import axiosSetup from '@/config/api.js'

import router from '@/routes/router.js'
import store from '@/store'

import '@/../css/fabriq.css'
import App from '@/App.vue'
import '@/block-types/index.js'
import '@/components/common-components.js'
import '@/directives/index.js'
import '@/filters/index.js'
import '@/icons/index.js'
import '@/plugins/index.js'

Vue.prototype.$eventBus = new Vue()

const app = new Vue({
    router,
    store,
    render: h => h(App)
}).$mount('#app')

axiosSetup(app)
