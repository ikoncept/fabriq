import Link from '@tiptap/extension-link'
export default class CustomLink extends Link {
    get schema () {
        return {
            attrs: {
                href: {
                    default: null
                },
                target: {
                    default: null
                }
            },
            inclusive: false,
            parseDOM: [
                {
                    tag: 'a[href]',
                    getAttrs: dom => ({
                        href: dom.getAttribute('href'),
                        target: dom.getAttribute('target')
                    })
                }
            ],
            toDOM: node => ['a', {
                ...node.attrs,
                rel: 'noopener noreferrer nofollow'
            }, 0]
        }
    }
}

// const CustomLink = Link.extend({
//     addKeyboardShortcuts() {
//       return {
//         'Mod-l': () => this.editor.commands.toggleBulletList(),
//       }
//     },
//   })
