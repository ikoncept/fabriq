<template>
    <Transition name="fade">
        <div v-if="moreThanOne"
             class="flex space-x-2"
        >
            <TransitionGroup name="fade"
                             tag="div"
                             class="flex space-x-2"
            >
                <div v-for="user in usersIdleWithoutKey"
                     :key="user.id"
                     v-tooltip.bottom="{ delay: { show: 100, hide: 100 }, content: authenticatedUser.id !== user.id ? `${user.name} tittar på denna sida nu` : 'Det är du!' }"
                >
                    <UiAvatar :user="user"
                              class="w-7 h-7"
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
            if (Object.values(this.usersIdle).length > 0) {
                return Object.values(this.usersIdle)[0]
            }
            return []
        },
        moreThanOne () {
            // return this.usersIdleWithoutKey.length > 1
            return true
        }
    }
}
</script>
