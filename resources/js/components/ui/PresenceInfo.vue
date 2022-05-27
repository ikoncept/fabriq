<template>
    <Transition name="fade">
        <div
            v-if="moreThanOne"
            class="flex items-center space-x-2"
        >
            <p
                v-show="currentUserIsFirst"
                class="text-xs italic text-neutral-600"
            >
                Sidan är låst eftersom en annan användare redigerar den
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
</template>
<script>
export default {
    name: 'PresenceInfo',
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
        currentUserIsFirst() {
            if(! this.moreThanOne) {
                return true
            }

            return this.authenticatedUser.id !== this.usersIdleWithoutKey[0].id
        }
    }
}
</script>
