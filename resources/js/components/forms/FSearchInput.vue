<template>
    <input type="search"
           :value="value"
           @input="updateValue($event.target.value)"
           @keydown.esc="clearValue"
    >
</template>
<script>
import { debounce } from '~/helpers/debounce'
export default {
    name: 'FSearchInput',
    props: {
        value: {
            type: String,
            required: true,
            default: ''
        }
    },
    data () {
        return {
            search: ''
        }
    },
    watch: {
        search: debounce(function (newValue) {
            this.$emit('perform-search')
        }, 250)
    },
    methods: {
        clearValue () {
            this.$emit('clear-search')
            this.$emit('input', '')
        },
        updateValue (value) {
            console.log('updating')
            this.search = value
            this.$emit('input', value)
        }
    }

}
</script>
