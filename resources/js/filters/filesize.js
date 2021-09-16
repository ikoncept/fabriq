import Vue from 'vue'
import filesize from 'filesize'

Vue.filter('filesize', function (value, options = {}) {
    if (!value) {
        return '0 B'
    }
    const defaults = {
        locale: 'sv'
    }
    const mergedOptions = {
        ...defaults,
        ...options
    }
    return filesize(value, mergedOptions)
})
