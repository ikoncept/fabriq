<template>
    <div class="flex h-screen overflow-hidden antialiased bg-gray-50">
        <FileModal />
        <ImageModal />
        <VideoModal />
        <UiSidebar />
        <UiDesktopSidebar />
        <UiTopbar />
        <div class="flex flex-col flex-1 w-0 mt-12 overflow-hidden lg:mt-0">
            <main class="relative flex-1 overflow-y-auto focus:outline-none"
                  tabindex="0"
            >
                <BlockTypeModal />
                <div class="py-6">
                    <div class="px-4 pb-24 mx-auto max-w-10xl sm:px-6 md:px-8">
                        <Transition mode="out-in"
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
import UiSidebar from '~/components/ui/UiSidebar'
import UiTopbar from '~/components/ui/UiTopbar'
import UiDesktopSidebar from '~/components/ui/UiDesktopSidebar'
import ImageModal from '~/images/ImageModal'
import FileModal from '~/files/FileModal'
import BlockTypeModal from '~/pages/BlockTypeModal'
import VideoModal from '~/videos/VideoModal'
import CommentSection from '~/comments/CommentSection'
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
            pollingNotifications: null
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
    created () {
        this.$store.dispatch('user/index')
        this.$store.dispatch('config/index')
        this.$store.dispatch('user/notifications')
        this.startPoll()
    },
    methods: {
        startPoll () {
            this.pollingNotifications = setInterval(() => {
                this.$store.dispatch('user/notifications')
            }, 1000 * 15)
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
