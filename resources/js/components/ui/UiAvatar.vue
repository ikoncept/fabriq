<template>
    <img
        class="rounded-full"
        :src="src"
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
                    email: '',
                    image: {
                        data: {}
                    }
                }
            },
            required: true
        }
    },
    computed: {
        fallbackUrl () {
            return encodeURIComponent(`https://eu.ui-avatars.com/api?name=${this.user.name}&format=svg&bold=true&background=e2d3bb&color=0b3b5b`)
        },

        src () {
            return this.user.image.data.thumb_src ?? `https://unavatar.now.sh/${this.user.email}?fallback=${this.fallbackUrl}`
        }
    }
}
</script>
