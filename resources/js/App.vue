<template>
    <div class="flex h-screen overflow-hidden antialiased bg-gray-50">
        <FileModal />
        <ImageModal />
        <VideoModal />
        <UiSidebar />
        <UiDesktopSidebar />
        <UiTopbar />

        <div class="flex flex-col flex-1 w-0 mt-12 overflow-hidden lg:mt-0">
            <main
                class="relative flex-1 overflow-y-auto focus:outline-none"
                tabindex="0"
            >
                <BlockTypeModal />
                <div class="py-6">
                    <div class="px-4 pb-24 mx-auto max-w-10xl sm:px-6 md:px-8">
                        <Transition
                            mode="out-in"
                            name="fade"
                        >
                            <KeepAlive>
                                <RouterView
                                    :key="$route.fullPath"
                                />
                            </KeepAlive>
                        </Transition>
                    </div>
                </div>
                <Transition name="fade">
                    <div v-if="$route.meta.commentable">
                        <CommentSection />
                    </div>
                </Transition>
            </main>
        </div>
    </div>
</template>
<script>
import UiSidebar from '@/components/ui/UiSidebar.vue'
import UiTopbar from '@/components/ui/UiTopbar.vue'
import UiDesktopSidebar from '@/components/ui/UiDesktopSidebar.vue'
import ImageModal from '@/images/ImageModal.vue'
import FileModal from '@/files/FileModal.vue'
import BlockTypeModal from '@/pages/BlockTypeModal.vue'
import VideoModal from '@/videos/VideoModal.vue'
import CommentSection from '@/comments/CommentSection.vue'
export default {
    name: 'App',
    components: {
        UiSidebar,
        UiDesktopSidebar,
        UiTopbar,
        ImageModal,
        BlockTypeModal,
        FileModal,
        VideoModal,
        CommentSection
    },
    data () {
        return {
            pollingNotifications: null,
            countdown: 0,
            cancelReload: false
        }
    },
    computed: {
        user () {
            return this.$store.getters['user/user']
        },
        userRoles () {
            return this.$store.getters['user/userRoles']
        }
    },
    async created () {
        await this.$store.dispatch('user/index')
        this.$store.dispatch('config/index')
        this.$store.dispatch('user/notifications')
        this.startPoll()
    },
    methods: {
        startPoll () {
            if(this.$echo) {
                // Listen to private user events

                this.listenToEchoEvents();
                return
            }
            this.pollingNotifications = setInterval(() => {
                this.$store.dispatch('user/notifications')
            }, 1000 * 15)
        },
        listenToEchoEvents() {
            const wsPrefix = window.fabriqCms.pusher.ws_prefix
            this.$echo.channel(`${wsPrefix}.comments`)
                .listen(`.comment.posted`, (event) => {
                    if (this.$store.getters['user/user'].id !== event.comment.user_id) {
                        this.$eventBus.$emit('comment-posted-echo', event)
                    }
                })
                .listen(`.comment.deleted`, (event) => {
                    if (this.$store.getters['user/user'].id !== event.comment.user_id) {
                        this.$eventBus.$emit('comment-posted-echo', event)
                    }
                })

            this.$echo.private(`${wsPrefix}.user.${this.$store.getters['user/user'].id}`)
                .listen(`.comment.user-mentioned`, (event) => {
                    this.$eventBus.$emit('user-mentioned-echo', event)
                    this.$store.dispatch('user/notifications')
                })
                .listen(`.notification.deleted`, (event) => {
                    this.$eventBus.$emit('user-mentioned-echo', event)
                    this.$store.dispatch('user/notifications')
                })
                .notification((notification) => {
                    // broadcast.ask-to-leave
                    if(notification.type === 'broadcast.ask-to-leave') {
                        this.$eventBus.$emit('user-asked-to-leave-echo', notification)
                    }
                    if(notification.type === 'broadcast.leave-declined') {
                        this.$eventBus.$emit('user-declined-to-leave-echo', notification)
                    }
                })
            this.$echo.channel(`${wsPrefix}.media`)
                .listen(`.media-finished-processing`, (event) => {
                    console.log(event)
                    this.$eventBus.$emit('media-finished-processing', event)
                })
        }
    }
}
</script>
<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>
