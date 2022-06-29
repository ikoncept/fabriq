/* eslint-disable  */
export default {
    methods: {
    // ––––––––––––––––––––––––––––––––––––
    // Getter methods
    // ––––––––––––––––––––––––––––––––––––
        getPathById (id, items = this.value) {
            let path = []

            items.every((item, index) => {
                if (item[this.keyProp] === id) {
                    path.push(index)
                } else if (item[this.childrenProp]) {
                    const childrenPath = this.getPathById(id, item[this.childrenProp])

                    if (childrenPath.length > 0) {
                        path = path.concat(index).concat(childrenPath)
                    }
                }

                return path.length === 0
            })

            return path
        },

        getItemByPath (path, items = this.value) {
            let item = null

            path.forEach(index => {
                const list = item && item[this.childrenProp] ? item[this.childrenProp] : items
                item = list[index]
            })

            return item
        },

        getItemDepth (item) {
            let level = 1

            if (item[this.childrenProp] && item[this.childrenProp].length > 0) {
                const childrenDepths = item[this.childrenProp].map(this.getItemDepth)
                level += Math.max(...childrenDepths)
            }

            return level
        },

        getSplicePath (path, options = {}) {
            const splicePath = {}
            const numberToRemove = options.numToRemove || 0
            const itemsToInsert = options.itemsToInsert || []
            const lastIndex = path.length - 1
            let currentPath = splicePath

            path.forEach((index, index_) => {
                if (index_ === lastIndex) {
                    currentPath.$splice = [[index, numberToRemove, ...itemsToInsert]]
                } else {
                    const nextPath = {}
                    currentPath[index] = { [options.childrenProp]: nextPath }
                    currentPath = nextPath
                }
            })

            return splicePath
        },

        getRealNextPath (previousPath, nextPath) {
            const ppLastIndex = previousPath.length - 1
            const npLastIndex = nextPath.length - 1

            if (previousPath.length < nextPath.length) {
                // move into deep
                let wasShifted = false

                return nextPath.map((nextIndex, index) => {
                    if (wasShifted) {
                        return index === npLastIndex? nextIndex + 1: nextIndex
                    }

                    if (typeof previousPath[index] !== 'number') {
                        return nextIndex
                    }

                    if (nextPath[index] > previousPath[index] && index === ppLastIndex) {
                        wasShifted = true
                        return nextIndex - 1
                    }

                    return nextIndex
                })
            } else if (previousPath.length === nextPath.length) {
                // if move bottom + move to item with children => make it a first child instead of swap
                if (nextPath[npLastIndex] > previousPath[npLastIndex]) {
                    const target = this.getItemByPath(nextPath)

                    if (target[this.childrenProp] && target[this.childrenProp].length > 0 && !this.isCollapsed(target)) {
                        return [...nextPath
                            .slice(0, -1)
                            .concat(nextPath[npLastIndex] - 1), 0]
                    }
                }
            }

            return nextPath
        }

        // getItemOptions() {
        //   const { renderItem, renderCollapseIcon, handler, childrenProp } = this.props;
        //   const { dragItem } = this.state;

        //   return {
        //     dragItem,
        //     childrenProp,
        //     renderItem,
        //     renderCollapseIcon,
        //     handler,

        //     onDragStart: this.onDragStart,
        //     onMouseEnter: this.onMouseEnter,
        //     isCollapsed: this.isCollapsed,
        //     onToggleCollapse: this.onToggleCollapse
        //   };
        // }

    }
}
