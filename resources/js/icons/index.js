import Vue from 'vue'

// import Vue from 'vue'
// import Vuex from 'vuex'
// Vue.use(Vuex)

// const globContext = import.meta.glob('./modules/*.js')
// // const requireContext = require.context('./modules', false, /.*\.js$/)


// // const modules = requireContext.keys()
// const modules = Object.keys(globContext).map(file =>
//     [file.replace(/(^.\/)|(\.js$)/g, ''), globContext[file]]
// )
//     .reduce((modules, [name, module]) => {
//         if (module.namespaced === undefined) {
//             module.namespaced = true
//         }

//         return { ...modules, [name]: module }
//     }, {})

// export default new Vuex.Store({
//     modules
// })

// Auto import everything in this folder
const request = import.meta.globEager('./*.vue')
Object.keys(request).map(key => {
    const name = key.match(/\w+/)[0]
    return Vue.component(name, request[key].default)
})
