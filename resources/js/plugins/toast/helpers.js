const removeElement = (element) => {
    if (typeof element.remove !== 'undefined') {
        element.remove()
    } else {
        element.parentNode.removeChild(element)
    }
}

export { removeElement }
