export function debounce (function_, delay) {
    let timeoutID = null
    return function () {
        clearTimeout(timeoutID)
        const arguments_ = arguments
        const that = this
        timeoutID = setTimeout(function () {
            function_.apply(that, arguments_)
        }, delay)
    }
}
