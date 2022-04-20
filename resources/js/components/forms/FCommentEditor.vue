<template>
    <div>
        <EditorContent
            v-if="editor"
            ref="editor"
            :editor="editor"
            class="w-full rounded focus:outline-none focus:ring-1 f-comment-editor"
        />
        <p
            v-if="helpText"
            class="mt-1 text-xs text-gray-500 help-text"
            v-text="helpText"
        />
    </div>
</template>
<script>
import tippy from 'tippy.js'
import 'tippy.js/themes/light-border.css'
import { Editor, EditorContent, VueRenderer } from '@tiptap/vue-2'
import StarterKit from '@tiptap/starter-kit'
import Placeholder from '@tiptap/extension-placeholder'
import Mention from '@tiptap/extension-mention'
import MentionList from '~/components/forms/extensions/MentionList'
import User from '~/models/User'

const CustomMention = Mention.extend({
    addAttributes () {
        return {
            id: {
                default: ''
            },
            'data-email': {
                default: ''
            }
        }
    }
})

export default {
    name: 'FCommentEditor',
    components: {
        EditorContent
    },
    props: {
        label: {
            type: String,
            default: 'Innehåll'
        },
        value: {
            type: [String, null],
            default: ' '
        },
        sticky: {
            type: Boolean,
            default: false
        },
        alignmentControls: {
            type: Boolean,
            default: false
        },
        name: {
            type: String,
            default: 'name'
        },
        placeholder: {
            type: String,
            default: ''
        },
        helpText: {
            type: String,
            default: ''
        },
        rows: {
            type: Number,
            default: 3
        }
    },
    data () {
        return {
            editor: null,
            isEditing: true,
            users: [],
            pop: ''
        }
    },
    computed: {
        userNames () {
            return this.users.map(item => {
                return { name: item.name, email: item.email }
            })
        }
    },
    watch: {
        value (value) {
            const isSame = this.editor.getHTML() === value

            if (isSame) {
                return
            }

            this.editor.commands.setContent(value, false)
        }
    },
    mounted () {
        this.$eventBus.$on('comment-posted', this.clearContent)
        this.fetchUsers()
        setTimeout(() => {
            this.initEditor()
        }, 100)
    },
    beforeDestroy () {
        this.$eventBus.$off('comment-posted', this.clearContent)
        this.editor.destroy()
    },
    methods: {
        async fetchUsers () {
            try {
                const { data } = await User.index()
                this.users = data
            } catch (error) {
                console.log(error)
            }
        },
        clearContent () {
            this.editor.commands.clearContent(true)
        },
        initEditor () {
            this.editor = new Editor({
                editorProps: {
                    handleDOMEvents: {
                        keydown: (view, event) => {
                            return event.keyCode === 13 && event.metaKey
                        }
                    }
                },
                content: this.value,
                onUpdate: () => {
                    // HTML
                    this.$emit('input', this.editor.getHTML())

                    // JSON
                    // this.$emit('update:modelValue', this.editor.getJSON())
                },
                extensions: [
                    StarterKit,
                    Placeholder.configure({
                        placeholder: this.placeholder
                    }),

                    CustomMention.configure({
                        HTMLAttributes: {
                            class: 'mention',
                            'data-email': ''
                        },
                        suggestion: {
                            items: ({ query }) => {
                                return this.userNames.filter(item => item.name.toLowerCase().startsWith(query.toLowerCase())).slice(0, 5)
                            },
                            render: () => {
                                let component
                                let popup

                                return {
                                    onStart: properties => {
                                        component = new VueRenderer(MentionList, {
                                            parent: this,
                                            propsData: properties
                                        })

                                        popup = tippy('body', {
                                            getReferenceClientRect: properties.clientRect,
                                            appendTo: () => document.body,
                                            content: component.element,
                                            showOnCreate: true,
                                            interactive: true,
                                            trigger: 'manual',
                                            placement: 'bottom-start'
                                        })
                                    },
                                    onUpdate (properties) {
                                        component.updateProps(properties)

                                        popup[0].setProps({
                                            getReferenceClientRect: properties.clientRect
                                        })
                                    },
                                    onKeyDown (properties) {
                                        if (properties.event.key === 'Escape') {
                                            popup[0].hide()

                                            return true
                                        }

                                        return component.ref?.onKeyDown(properties)
                                    },
                                    onExit () {
                                        popup[0].destroy()
                                        component.destroy()
                                    }
                                }
                            }
                        }
                    })
                ]
            })
            // this.editor.on('update', () => {
            //     this.$emit('input', this.editor.getHTML())
            // })

            this.editor.on('focus', ({ editor, event }) => {
                this.$emit('focus', event)
            })
            this.editor.on('blur', ({ editor, event }) => {
                this.$emit('blur', event)
            })
            // this.editor.commands.setContent(this.value)
        }
    }
}
</script>
<style>
.f-comment-editor > div {
    @apply w-full px-4 pt-2 pb-2 transition duration-200 ease-out bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 ring-inset ring-gray-800;
}
.f-comment-editor .tippy-content .items {
    @apply flex flex-col;
}
.mention {
    color: theme("colors.purple.600");
    font-weight: theme('fontWeight.medium');
    background: theme("colors.transparent");
}
.items {
    position: relative;
    border-radius: 0.25rem;
    background: white;
    overflow: hidden;
    @apply shadow text-sm text-neutral-600;
}
.item {
    display: block;
    width: 100%;
    text-align: left;
    background: transparent;
    border: none;
    padding: 0.2rem 0.5rem;
    @apply font-medium;
}

.item.is-selected,
.item:hover {
    @apply bg-purple-50 text-purple-600;
}
/* Placeholder (at the top) */
.ProseMirror p.is-editor-empty:first-child::before {
    content: attr(data-placeholder);
    float: left;
    pointer-events: none;
    height: 0;
    @apply text-gray-400;
}
</style>
