<template>
    <div
        class="fixed bottom-0 right-0 w-4/5 bg-white border-l border-gray-200 rounded-tl xl:w-1/2 lg:w-2/3 min-h-10 comment-box"
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
            :duration="0"
            @open-start="scrollToLatestComment"
        >
            <div>
                <div ref="comments"
                     class="flow-root overflow-y-auto h-96"
                >
                    <ul
                        role="list"
                        class="px-8 pt-6 -mb-8"
                    >
                        <li v-for="(comment, index) in comments"
                            :key="comment.id"
                        >
                            <div class="relative pb-8">
                                <span v-if="(index+1) < comments.length"
                                      class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-royal-500"
                                      aria-hidden="true"
                                />
                                <div class="relative flex items-start space-x-3">
                                    <div class="relative">
                                        <UiAvatar :email="comment.user.data.email"
                                                  class="flex items-center justify-center w-10 h-10 bg-gray-400 rounded-full ring-1 ring-gold-500"
                                        />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div>
                                            <div class="text-sm">
                                                <a href="#"
                                                   class="font-medium text-gray-900"
                                                >{{ comment.user.data.name }}</a>
                                            </div>
                                            <p class="mt-0.5 text-xs text-gray-500">
                                                för {{ comment.created_at | localTime(null, true) }}
                                            </p>
                                        </div>
                                        <div class="pr-4 mt-2 text-sm text-gray-700">
                                            <div v-html="comment.comment" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="relative z-20 flex items-end mx-6 my-6 space-x-6">
                        <FCommentEditor v-if="commentSectionOpen"
                                        v-model="newComment"
                                        class="flex-1"
                                        placeholder="Skriv din kommentar här…"
                        />
                        <div>
                            <FButton :click="postComment"
                                     class="px-6 py-2.5 leading-none fabriq-btn btn-royal z-50"
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
import Comment from '~/models/Comment'
export default {
    name: 'CommentSection',
    beforeRouteLeave () {
        console.log('bye from comment section')
    },
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
        this.$eventBus.$on('open-comment-section', this.openCommentSection)
    },
    beforeDestroy () {
        this.$eventBus.$off('open-comment-section', this.openCommentSection)
    },
    methods: {
        openCommentSection () {
            this.commentSectionOpen = true
        },
        scrollToLatestComment () {
            setTimeout(() => {
                const container = this.$refs.comments
                // container.scrollTop = container.scrollHeight
                container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' })
            }, 150)
            // console.log(container.scrollTop)
        },
        async postComment () {
            try {
                const payload = {
                    comment: this.newComment,
                    params: {
                        include: 'user'
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
                        include: 'user'
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
