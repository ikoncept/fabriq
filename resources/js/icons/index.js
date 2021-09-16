import Vue from 'vue'

// Auto import everything in this folder
const request = require.context('./', true, /\.(js|vue)$/i)
request.keys().map(key => {
    const name = key.match(/\w+/)[0]
    return Vue.component(name, request(key).default)
})
