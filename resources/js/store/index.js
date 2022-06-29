import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const vuexModules = import.meta.globEager('./modules/*.js')

// Load store modules dynamically.
// const requireContext = require.context('./modules', false, /.*\.js$/)

const modules = Object.keys(vuexModules)
    .map(file => [file.replace(/(^.\/modules\/)|(\.js$)/g, ''), vuexModules[file]])
    .reduce((modules, [name, module]) => {
        const actualModule = Object.assign({ namespaced: true }, module)

        return {
            ...modules,
            [name]: actualModule
        }
    }, {})

export default new Vuex.Store({
    modules
})
