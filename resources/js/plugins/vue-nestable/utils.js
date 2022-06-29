export const closest = (target, selector) => {
    return target.closest(selector)
}

export const getOffsetRect = (element) => {
    var box = element.getBoundingClientRect()

    return { top: Math.round(box.top), left: Math.round(box.left) }
}

export const getTransformProperties = (x, y) => {
    return {
        transform: 'translate(' + x + 'px, ' + y + 'px)'
    }
}

export const listWithChildren = (list, childrenProperty) => {
    return list.map(item => {
        return {
            ...item,
            [childrenProperty]: item[childrenProperty]? listWithChildren(item[childrenProperty], childrenProperty): []
        }
    })
}
