import { mergeAttributes, Node } from '@tiptap/core'

const inputRegex = /!\[(.+|:?)]\((\S+)(?:(?:\s+)["'](\S+)["'])?\)/

export default Node.create({
    name: 'iframe',

    defaultOptions: {
        HTMLAttributes: {
            frameborder: 0,
            allowfullscreen: true,
            allow: 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture',
            width: '560',
            height: '315',
            src: 'https://golfair.dk/player/kristiansstads-golfklubb/ostra/'
        }
    },

    atom: true,
    group: 'block',
    selectable: false,

    parseHTML () {
        return [{ tag: 'iframe[src]' }]
    },

    renderHTML ({ HTMLAttributes }) {
        const videoClasses = HTMLAttributes.src.includes('youtube') ? 'aspect-w-16 aspect-h-9' : ''
        return [
            'div',
            {
                class: 'embed-container ' + videoClasses
            },
            ['iframe', mergeAttributes(this.options.HTMLAttributes, HTMLAttributes)]
        ]
    },

    addAttributes () {
        return {
            src: {
                default: ''
            },
            height: {
                default: 'auto'
            },
            width: {
                default: '100%'
            },
            class: {
                default: ''
            }
        }
    },

    // addNodeView () {
    //     return VueNodeViewRenderer(YouTubeEditor)
    // },

    addCommands () {
        return {
            setIframe: options => ({ tr, dispatch }) => {
                if (dispatch) {
                    tr.replaceSelectionWith(this.type.create(options)).scrollIntoView()
                }
                return true
            }
        }
    }
    // addInputRules () {
    //     return [
    //         nodeInputRule(inputRegex, this.type, match => {
    //             const [, alt, source, title, height, width, onload, sizes] = match

    //             return { src: source, alt, title, height, width, onload, sizes }
    //         })
    //     ]
    // }
    // addInputRules() {
    //     return [
    //       nodeInputRule(inputRegex, this.type, match => {
    //         const [, alt, src, title] = match

    //         return { src, alt, title }
    //       }),
    //     ]
    //   },

})
