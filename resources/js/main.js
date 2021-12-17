import Vue from 'vue'

import axiosSetup from '~/config/api'

import router from '~/routes/router'
import store from '~/store'

import App from '~/App'
import '~/directives'
import '~/filters'
import '~/icons'
import '~/plugins'
import '~/components/common-components'
import '~/block-types'

Vue.prototype.$eventBus = new Vue()

const app = new Vue({
    router,
    store,
    render: h => h(App)
}).$mount('#app')

axiosSetup(app)
