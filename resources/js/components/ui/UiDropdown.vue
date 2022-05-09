<template>
    <div
        v-click-outside="close"
        class="relative inline-flex text-left"
    >
        <button
            type="button"
            class="focus:outline-none"
            @click.stop="toggleShow"
        >
            <slot />
        </button>
        <Transition
            enter-active-class="transition duration-100 ease-out"
            enter-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <div
                v-show="show"
                class="absolute z-50 bg-white rounded shadow-lg ring-1 ring-gray-800 ring-opacity-30"
                :class="[{'top-full -left-px': alignment == 'top-left', ' right-0 top-full': alignment == 'top-right'}, marginClasses]"
            >
                <slot name="dropdown" />
            </div>
        </Transition>
    </div>
</template>
<script>
export default {
    name: 'UiDropdown',
    props: {
        alignment: {
            type: String,
            default: 'top-left'
        },
        marginClasses: {
            type: String,
            default: 'mt-3'
        },
        disabled: {
            type: Boolean,
            default: false
        }
    },
    data () {
        return {
            show: false
        }
    },
    watch: {
        show (value) {
            if (value) {
                this.$emit('open')
            }
            if (!value) {
                this.$emit('close')
            }
        }
    },
    methods: {
        close () {
            this.show = false
        },
        open () {
            this.show = true
        },
        toggleShow () {
            if (!this.disabled) {
                this.show = !this.show
            }
        }
    }
}
</script>
