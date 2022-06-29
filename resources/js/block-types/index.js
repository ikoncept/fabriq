import Vue from 'vue'

// Auto import everything in this folder


const request = import.meta.globEager('./*.vue')
Object.keys(request).map(key => {
    const name = key.match(/\w+/)[0]
    return Vue.component(name, request[key].default)
})
