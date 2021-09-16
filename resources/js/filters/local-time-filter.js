import { format, formatDistance, isValid, parseISO } from 'date-fns'
import svLocale from 'date-fns/locale/sv'
import Vue from 'vue'

Vue.filter('localTime', function (value, dateFormat = null, formatDistanceEnabled = false) {
    let date = null
    if (value instanceof Date) {
        date = value
    } else {
        date = parseISO(value)
        if (!isValid(date)) {
            console.warn('Not a valid date: ', value)
            return value
        }
    }
    if (formatDistanceEnabled) {
        return formatDistance(date, new Date(), { locale: svLocale, addSuffix: true })
    }
    if (dateFormat) {
        return format(date, dateFormat, { locale: svLocale })
    }
    return format(date, 'yyyy-MM-dd HH:mm')
})
