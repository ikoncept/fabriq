<template>
    <div>
        <RefreshObjectModal name="askToLeaveModal">
            <template #title>
                Lämna över kontroll
            </template>
            <template #default="{ params }">
                <div>
                    <div class="text-center">
                        <p
                            v-if="params.causer"
                            class="my-3 text-sm"
                        >
                            <span class="font-medium">{{ params.causer.name }}</span> efterfrågar att redigera sidan.
                        </p>
                        <HelpText>Om du lämnar över går osparade ändringar förlorade</HelpText>
                    </div>
                    <div class="flex justify-center mt-8">
                        <div class="space-x-4 whitespace-nowrap">
                            <FButton
                                class="px-6 py-2.5 leading-none text-sm fabriq-btn btn-outline-royal"
                                without-loader
                                :click="() => declineEdit(params)"
                            >
                                Fortsätt redigera
                            </FButton>
                            <FButton
                                class="px-6 py-2.5 leading-none fabriq-btn btn-royal"
                                :click="refreshPage"
                            >
                                Lämna över
                            </FButton>
                        </div>
                    </div>
                </div>
            </template>
            <template #actions>
                <div>&nbsp;</div>
            </template>
        </RefreshObjectModal>
        <Transition name="fade">
            <div
                v-if="moreThanOne"
                class="flex items-start space-x-2 min-h-6"
            >
                <p
                    v-show="currentUserIsFirst"
                    class="text-xs italic text-right text-neutral-600"
                >
                    Sidan är låst eftersom en annan användare redigerar den. <br>
                    <span
                        v-if="!hasAskedToEdit"
                        class="not-italic font-bold cursor-pointer text-royal-500"
                        @click="askToEdit"
                    >Be om att få redigera</span>
                    <span
                        v-else
                        class="not-italic font-bold text-neutral-500"
                    >Förfrågan skickad</span>
                </p>
                <TransitionGroup
                    name="fade"
                    tag="div"
                    class="flex -space-x-2"
                >
                    <div
                        v-for="(user, index) in usersIdleWithoutKey"
                        :key="user.id"
                        v-tooltip.bottom="{ delay: { show: 100, hide: 100 }, content: authenticatedUser.id !== user.id ? `${user.name} tittar på denna sida nu` : 'Det är du!' }"
                        :class="{'z-10' : index === 0}"
                    >
                        <UiAvatar
                            :user="user"
                            class="object-cover border rounded-full w-7 h-7 "
                        />
                    </div>
                </TransitionGroup>
            </div>
        </Transition>
    </div>
</template>
<script>
import RefreshObjectModal from '@/components/modals/RefreshObjectModal.vue'
import axios from 'axios'
export default {
    name: 'PresenceInfo',
    components: { RefreshObjectModal },
    data() {
        return {
            hasAskedToEdit: false
        }
    },
    computed: {
        authenticatedUser () {
            return this.$store.getters['user/user']
        },
        usersIdle () {
            return this.$store.getters['echo/usersIdle']
        },
        usersIdleWithoutKey () {
            return Object.values(this.usersIdle)[0]
        },
        moreThanOne () {
            if(!this.usersIdleWithoutKey) {
                return false
            }
            return this.usersIdleWithoutKey.length > 1
        },
        firstUser() {
            if(this.moreThanOne) {
                return this.usersIdleWithoutKey[0]
            }
            return null
        },
        currentUserIsFirst() {
            if(! this.moreThanOne) {
                return true
            }

            return this.authenticatedUser.id !== this.usersIdleWithoutKey[0].id
        }
    },
    created() {
        this.$eventBus.$on('user-asked-to-leave-echo', this.notifyUser)
        this.$eventBus.$on('user-declined-to-leave-echo', this.notifyUserLeaveDeclined)
    },
    beforeDestroy() {
        this.$eventBus.$off('user-asked-to-leave-echo', this.notifyUser)
        this.$eventBus.$off('user-declined-to-leave-echo', this.notifyUserLeaveDeclined)
    },
    methods: {
        async askToEdit() {
            await axios.post('/api/notifications/ask-to-leave/' + this.firstUser.id, {
                path: this.$route.path
            })
            this.hasAskedToEdit = true
        },
        async refreshPage() {
            window.location.reload()
        },
        async declineEdit(parameters) {
            await axios.post('/api/notifications/decline-to-leave/' + parameters.causer.id, {
                path: this.$route.path
            })
            this.$vfm.hide('askToLeaveModal')
        },
        notifyUserLeaveDeclined(event) {
            if(this.$route.path !== event.identifier) {
                return
            }
            this.$toast.declined({
                position: 'top-right',
                title: 'Överlämning nekad',
                message: event.text,
                countdown: false,
                duration: 10 * 1000
            })
            this.hasAskedToEdit = false
        },
        notifyUser(event) {
            if(event.identifier === this.$route.path) {
                this.$vfm.show('askToLeaveModal', event)
            }
        },
    }
}
</script>
