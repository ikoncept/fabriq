<template>
    <span
        :class="[classes]"
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
    >
        <slot />
    </span>
</template>
<script>
export default {
    name: 'UiBadge',
    props: {
        color: {
            type: String,
            required: false,
            default: ''
        },
        highContrast: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        classes () {
            if (this.highContrast) {
                if (this.color === 'gray') {
                    return 'bg-gray-100 text-gray-800'
                }
                if (this.color === 'red') {
                    return 'bg-red-800 text-white  subpixel-antialiased'
                }
                if (this.color === 'yellow') {
                    return 'bg-yellow-400 text-black font-semibold subpixel-antialiased'
                }
                if (this.color === 'green') {
                    return 'bg-green-800 text-white font-semibold subpixel-antialiased'
                }

                return 'bg-gold-200 text-gray-800'
            }
            if (this.color === 'gray') {
                return 'bg-gray-100 text-gray-800'
            }
            if (this.color === 'red') {
                return 'bg-red-100 text-red-800'
            }
            if (this.color === 'yellow') {
                return 'bg-yellow-100 text-yellow-800'
            }
            if (this.color === 'green') {
                return 'bg-green-100 text-green-800'
            }
            if (this.color === 'royal') {
                return 'bg-royal-100 text-royal-800'
            }
            if (this.color === 'gold') {
                return 'bg-gold-500 text-white'
            }
            return 'bg-gold-200 text-gray-800'
        }
    }
}
</script>
