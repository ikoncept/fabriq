<template>
    <div
        ref="comments"
        class="fixed bottom-0 right-0 z-10 w-4/5 bg-white border-l border-gray-200 rounded-tl bgred-100 xl:w-1/2 lg:w-2/3 min-h-10 comment-box"
    >
        <div class="flex items-center justify-between text-gray-100 bg-gray-800 rounded-tl cursor-pointer min-h-10"
             @click.stop="commentSectionOpen = ! commentSectionOpen"
        >
            <div class="flex items-center ml-8">
                <CommentIcon class="w-6 h-6 " />
                <div class="ml-4 text-xs font-semibold">
                    {{ comments.length }} <span v-text="comments.length === 1 ? 'kommentar' : 'kommentarer'" />
                </div>
            </div>
            <button class="mr-6 text-sm font-semibold focus:outline-none"
                    v-text="commentSectionOpen ? 'Stäng' : 'Öppna'"
            />
        </div>
        <SlideUpDown
            ref="monkey"
            :active="commentSectionOpen"
            :duration="5"
            @open-start="scrollToLatestComment"
        >
            <div>
                <div ref="flow"
                     class="flow-root overflow-y-auto h-[660px]"
                >
                    <ul
                        role="list"
                        class="px-8 pt-6 -mb-8"
                    >
                        <li v-for="(comment, index) in comments"
                            :key="comment.id"
                        >
                            <CommentItem :comment="comment"
                                         :is-last="(index+1) === comments.length"
                                         @refresh-comments="fetchComments"
                            />
                        </li>
                    </ul>
                    <div class="relative z-20 flex items-center mx-8 mt-16 mb-8 space-x-4">
                        <FCommentEditor v-if="commentSectionOpen"
                                        v-model="newComment"
                                        class="flex-1"
                                        placeholder="Skriv din kommentar här…"
                                        @focus="onFocus"
                                        @blur="onBlur"
                        />
                        <div>
                            <FButton :click="postComment"
                                     class="px-6 py-2.5 leading-none fabriq-btn btn-royal z-50 h-[41px]"
                            >
                                Skicka
                            </FButton>
                        </div>
                    </div>
                </div>
            </div>
        </SlideUpDown>
    </div>
</template>
<script>
import CommentItem from '~/comments/CommentItem'
import Comment from '~/models/Comment'
import User from '~/models/User'
export default {
    name: 'CommentSection',
    components: { CommentItem },
    data () {
        return {
            commentSectionOpen: false,
            newComment: '',
            comments: []
        }
    },
    computed: {
        user () {
            return this.$store.getters['user/user']
        }
    },
    mounted () {
        this.fetchComments()
        this.fetchUsers()
        this.$eventBus.$on('open-comment-section', this.openCommentSection)
    },
    beforeDestroy () {
        this.$eventBus.$off('open-comment-section', this.openCommentSection)
        this.$refs.comments.removeEventListener('keydown', this.listenForMetaPlusEnter)
    },
    methods: {
        async fetchUsers () {
            try {
                const { data } = await User.index()
                this.$store.commit('user/SET_USERS', data)
                // this.users = data
            } catch (error) {
                console.log(error)
            }
        },
        onFocus () {
            this.$refs.comments.addEventListener('keydown', this.listenForMetaPlusEnter)
        },
        onBlur () {
            this.$refs.comments.removeEventListener('keydown', this.listenForMetaPlusEnter)
        },
        openCommentSection () {
            this.commentSectionOpen = true
        },
        scrollToLatestComment () {
            if (this.$route.query.commentId) {
                setTimeout(() => {
                    this.scrollToComment(this.$route.query.commentId)
                }, 300)
                return
            }
            const container = this.$refs.flow
            setTimeout(() => {
                container.scrollTo({ top: container.scrollHeight })
            }, 100)
        },
        scrollToComment (id) {
            const element = document.getElementById('comment' + id)
            element.scrollIntoView({ behavior: 'smooth', top: 10 })
        },
        listenForMetaPlusEnter (event) {
            const vm = this
            if (!(event.keyCode === 13 && event.metaKey)) return
            vm.postComment()
        },
        async postComment () {
            try {
                const payload = {
                    comment: this.newComment,
                    params: {
                        include: 'user,children,children.user'
                    }
                }
                await Comment.store('pages', this.$route.params.id, payload)
                this.newComment = ''
                this.fetchComments()
                this.$eventBus.$emit('comment-posted')
                setTimeout(() => {
                    this.scrollToLatestComment()
                }, 300)
            } catch (error) {
                console.error(error)
            }
        },
        async fetchComments () {
            try {
                const payload = {
                    params: {
                        include: 'user,children,children.user'
                    }
                }
                const { data } = await Comment.index('pages', this.$route.params.id, payload)
                this.comments = data
            } catch (error) {
                console.error(error)
            }
        }
    }
}
</script>
