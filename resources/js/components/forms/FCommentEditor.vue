<template>
    <div>
        <EditorContent
            v-if="editor"
            :editor="editor"
            class="w-full border-t rounded focus:outline-none focus:ring-1 f-comment-editor"
        />
    </div>
</template>
<script>
import tippy from 'tippy.js'
import 'tippy.js/themes/light-border.css'
import { Editor, EditorContent, VueRenderer } from '@tiptap/vue-2'
import Document from '@tiptap/extension-document'
import Paragraph from '@tiptap/extension-paragraph'
import Text from '@tiptap/extension-text'
import Placeholder from '@tiptap/extension-placeholder'
import Mention from '@tiptap/extension-mention'
import MentionList from '~/components/forms/extensions/MentionList'
import User from '~/models/User'

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
        }
    },
    data () {
        return {
            editor: null,
            isEditing: true,
            users: []
        }
    },
    computed: {
        userNames () {
            return this.users.map(item => {
                return item.name
            })
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
                extensions: [
                    Document,
                    Paragraph,
                    Text,
                    Placeholder.configure({
                        placeholder: this.placeholder
                    }),
                    Mention.configure({
                        HTMLAttributes: {
                            class: 'mention'
                        },
                        suggestion: {
                            items: query => {
                                return this.userNames.filter(item => item.toLowerCase().startsWith(query.toLowerCase())).slice(0, 5)
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
            this.editor.on('update', () => {
                this.$emit('input', this.editor.getHTML())
            })
            this.editor.commands.setContent(this.value)
        }
    }
}
</script>
<style>
    .f-comment-editor > div {
        @apply w-full px-4 pt-4 pb-4 transition duration-200 ease-out bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 ring-inset ring-gray-800 h-24 overflow-y-auto;
    }
    .f-comment-editor .tippy-content .items {
        @apply flex flex-col;
    }
.mention {
    color: #A975FF;
    border-radius: 0.3rem;
    padding: 0.1rem 0.3rem;
    @apply bg-purple-50;
}
    .items {
  position: relative;
  border-radius: 0.25rem;
  background: white;
  color: rgba(0,0,0, 0.8);
  overflow: hidden;
  font-size: 0.9rem;
  @apply shadow;
}
.item {
  display: block;
  width: 100%;
  text-align: left;
  background: transparent;
  border: none;
  padding: 0.2rem 0.5rem;
}

.item.is-selected, .item:hover {
    @apply bg-purple-50 text-purple-500;
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
