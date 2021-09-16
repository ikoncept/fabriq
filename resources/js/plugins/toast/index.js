import Vue from 'vue'

import Toast from '~/plugins/toast/Toast'
import Api from '~/plugins/toast/api.js'

const Plugin = (Vue, options = {}) => {
    const methods = Api(Vue, options)
    Vue.$toast = methods
    Vue.prototype.$toast = methods
}

Toast.install = Plugin

Vue.use(Toast, {
    // One of options
    dark: true,
    duration: 5000,
    position: 'bottom-right'
})
