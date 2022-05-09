<template>
    <div
        v-show="isActive"
        class="py-6"
    >
        <slot />
    </div>
</template>
<script>
export default {
    name: 'FTab',
    props: {
        title: {
            required: true,
            type: String,
            default: 'Tab'
        },
        valueKey: {
            type: String,
            default: ''
        },
        hasError: {
            type: Boolean,
            default: false
        }
    },
    data () {
        return {
            isActive: false
        }
    }
}
</script>
