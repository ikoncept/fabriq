<template>
    <label :class="{'text-white': nonWhiteBg }"
           :for="name"
           class="block font-sans text-sm font-medium text-gray-600 mb-1.5"
    >
        <slot /><span v-if="required"
                      class="-ml-1 text-red-500"
        >*</span><span v-if="showOptional"
                       class="italic text-gray-400"
                       v-text="'('+optional+')'"
        />
        <code v-if="devMode"><span class="text-xs">{{ name }}</span></code>
    </label>
</template>
<script>
export default {
    name: 'FLabel',
    props: {
        label: {
            type: String,
            default: ''
        },
        name: {
            type: String,
            default: 'name'
        },
        nonWhiteBg: {
            type: Boolean,
            default: false
        },
        required: {
            type: Boolean,
            default: false
        },
        optional: {
            type: String,
            default: ''
        }
    },
    computed: {
        devMode () {
            return this.$store.getters['config/devMode']
        },
        showOptional () {
            if (this.required && this.optional) return false
            if (!this.required && this.optional) return true
            return false
        }
    }
}
</script>
