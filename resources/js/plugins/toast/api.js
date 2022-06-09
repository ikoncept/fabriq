import eventBus from './bus.js'
import Toast from './Toast.vue'

const Api = (Vue, globalOptions = {}) => {
    return {
        open (options) {
            const defaultOptions = {
                title: '',
                dismissText: 'Stäng'
            }

            const propertiesData = Object.assign({}, defaultOptions, globalOptions, options)

            return new (Vue.extend(Toast))({
                el: document.createElement('div'),
                propsData: propertiesData
            })
        },
        clear () {
            eventBus.$emit('toast-clear')
        },
        success (options = {}) {
            return this.open(Object.assign({}, {
                type: 'success'
            }, options))
        },
        error (options = {}) {
            return this.open(Object.assign({}, {
                type: 'error'
            }, options))
        },
        info (options = {}) {
            return this.open(Object.assign({}, {
                type: 'info'
            }, options))
        },
        warning (options = {}) {
            return this.open(Object.assign({}, {
                type: 'warning'
            }, options))
        },
        declined (options = {}) {
            return this.open(Object.assign({}, {
                type: 'declined'
            }, options))
        },
        default (options = {}) {
            return this.open(Object.assign({}, {
                type: 'default'
            }, options))
        }
    }
}

export default Api
