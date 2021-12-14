<template>
    <img
        class="rounded-full"
        :src="`https://unavatar.now.sh/${user.email}?fallback=${fallbackUrl}`"
        :alt="`Profilbild för ${user.name}`"
    >
</template>
<script>
export default {
    name: 'UiAvatar',
    props: {
        user: {
            type: Object,
            default: () => {
                return {
                    name: '',
                    email: ''
                }
            },
            required: true
        }
    },
    computed: {
        fallbackUrl () {
            return `https://eu.ui-avatars.com/api/?background=e2d3bb&color=0b3b5b&name=${this.user.name}&format=svg&bold=false`
        }
    }
}
</script>
