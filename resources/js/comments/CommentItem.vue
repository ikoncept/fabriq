<template>
    <div
        :id="'comment'+comment.id"
        class="relative "
        style="scroll-margin-top: 10px;"
        :class="isChild ? 'pb-2 pr-4' : 'pb-6 pt-4 px-4 mb-4 bg-white shadow-lg rounded-md border border-neutral-100'"
    >
        <div class="relative flex items-start space-x-3">
            <div class="relative z-10">
                <UiAvatar
                    :user="comment.user.data"
                    :class="isChild ? 'w-6 h-6' : 'w-10 h-10'"
                    class="flex items-center justify-center object-cover mt-1.5 bg-gray-400 rounded-full ring-1 ring-royal-500"
                />
            </div>
            <div class="flex-1 min-w-0">
                <div>
                    <p class="mt-0.5 " />
                </div>
                <div
                    class="relative pr-4 text-sm text-gray-700"
                >
                    <div class="relative">
                        <div
                            v-if="!comment.anonmyzed_at"
                            v-click-outside="rollBackDelete"
                            class="relative inline-flex flex-col px-4 py-2 rounded-lg bg-neutral-100"
                        >
                            <div
                                v-if="isOwnedByUser && ! comment.anonmyzed_at"
                                class="absolute top-0 right-0 flex transition-all duration-300  items-center  h-6 p-[7.5px] rounded-full cursor-pointer overflow-hidden"
                                :class="isDeleting ? 'w-20 bg-neutral-200 shadow' : 'w-6'"
                                @click="deleteComment"
                            >
                                <XMarkIcon
                                    :class="isDeleting ? 'text-neutral-500' : 'text-neutral-400'"
                                    class="block group-hover:hidden max-w-2.5"
                                    regular
                                />
                                <span
                                    v-show="isDeleting"
                                    class="justify-center flex-1 w-full text-xs text-center whitespace-nowrap "
                                >Ta bort</span>
                            </div>
                            <div class="pr-4">
                                <span
                                    class="text-xs font-medium text-gray-900"
                                    v-text="isOwnedByUser ? 'Du' : comment.user.data.name"
                                />
                                <span class="text-xs text-gray-500">för {{ comment.created_at | localTime(null, true) }}</span>
                            </div>
                            <div
                                class="inline-flex flex-col prose-sm prose origin-left transform scale-95 "
                                v-html="comment.comment"
                            />
                        </div>
                        <div
                            v-else
                            class="pt-3 pb-2 prose-sm prose origin-left"
                        >
                            <span class="inline-block px-2 py-1 text-xs italic rounded-md text-neutral-600 ">
                                <span
                                    class="text-xs font-medium text-neutral-600 "
                                    v-text="isOwnedByUser ? 'Du' : comment.user.data.name"
                                />
                                tog bort kommentaren {{ comment.anonmyzed_at | localTime }}</span>
                        </div>
                    </div>
                    <div
                        v-if="!isChild"
                        class="relative mt-2 ml-2"
                    >
                        <CommentItem
                            v-for="(child, index) in comment.children.data"
                            :key="child.id"
                            is-child
                            :is-last="(index+1) === comment.children.data.length"
                            :comment="child"
                            @refresh-comments="$emit('refresh-comments')"
                        />
                    </div>
                    <span v-if="!isChild">
                        <div class="relative mt-4">
                            <FCommentEditor
                                ref="commentInput"
                                v-model="commentText"
                                class="flex-1"
                                placeholder="Svara…"
                                :rows="1"
                                @focus="onFocus"
                                @blur="onBlur"
                            />
                            <FButton
                                :class="replyHasFocus ? 'text-royal-500' : 'text-neutral-300'"
                                :click="postComment"
                                class="absolute bottom-0 right-0 pb-1.5 pr-2 mb-1"
                            ><PaperPlaneTopIcon
                                class="block w-5 h-5 "
                                :solid="replyHasFocus"
                            /></FButton>
                        </div>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Comment from '~/models/Comment'
export default {
    name: 'CommentItem',
    props: {
        isLast: {
            type: Boolean,
            default: false
        },
        isChild: {
            type: Boolean,
            default: false
        },
        comment: {
            type: Object,
            required: true,
            default: () => {
                return {
                    user: {
                        data: {}
                    },
                    children: {
                        data: []
                    }
                }
            }
        }
    },
    data () {
        return {
            isDeleting: false,
            commentText: '',
            replyHasFocus: false
        }
    },
    computed: {
        isOwnedByUser () {
            return this.user.email === this.comment.user.data.email
        },
        user () {
            return this.$store.getters['user/user']
        }
    },
    methods: {
        rollBackDelete () {
            this.isDeleting = false
        },
        onFocus () {
            this.replyHasFocus = true
            window.addEventListener('keydown', this.listenForMetaPlusEnter)
        },
        onBlur () {
            this.replyHasFocus = false
            window.removeEventListener('keydown', this.listenForMetaPlusEnter)
        },
        listenForMetaPlusEnter (event) {
            const vm = this
            if (!(event.keyCode === 13 && event.metaKey)) return
            vm.postComment()
        },
        async postComment () {
            try {
                const payload = {
                    comment: this.commentText,
                    parent_id: this.comment.id,
                    params: {
                        include: 'user,children,children.user'
                    }
                }
                await Comment.store('pages', this.$route.params.id, payload)
                this.$emit('refresh-comments')
                this.commentText = ''
            } catch (error) {
                console.error(error)
            }
        },
        async deleteComment () {
            if (!this.isDeleting) {
                this.isDeleting = true
                return
            }
            try {
                await Comment.destroy(this.comment.id)
                this.$emit('refresh-comments')
                this.$toast.success({ title: 'Kommentaren har raderats!' })
            } catch (error) {
                console.error(error)
                this.isDeleting = false
            }
        }
    }
}
</script>
