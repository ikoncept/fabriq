<template>
    <div :class="{'opacity-70': inputDisabled}">
        <FLabel
            v-if="columnLayout"
            class="mb-2 cursor-pointer select-none"
            :name="name"
        >
            <slot />
        </FLabel>
        <div
            class="flex items-center"
        >
            <span
                v-if="hasSlot && ! columnLayout"
                :name="name"
                class="flex flex-col mr-4 "
                @click="computedValue = ! computedValue"
            >
                <FLabel
                    class="mb-0 cursor-pointer select-none"
                    :name="name"
                >
                    <slot />
                </FLabel>
            </span>
            <!-- On: "bg-gold-600", Off: "bg-gray-200" -->
            <button
                :class="{'bg-royal-500': computedValue, 'bg-gray-200': !computedValue}"
                :aria-labelledby="name"
                aria-pressed="false"
                class="relative inline-flex flex-shrink-0 h-6 transition-colors duration-200 ease-in-out border-2 border-transparent rounded-full cursor-pointer w-11 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-royal-200"
                type="button"
                @click="toggleSwitch"
            >
                <span class="sr-only">Use setting</span>
                <!-- On: "translate-x-5", Off: "translate-x-0" -->
                <span
                    :class="{'translate-x-5': computedValue, 'translate-x-0':! computedValue}"
                    aria-hidden="true"
                    class="absolute top-0 left-0 inline-block w-5 h-5 transition duration-200 ease-in-out transform bg-white rounded-full shadow ring-0"
                />
            </button>
            <Transition
                v-if="columnLayout"
                tag="div"
                name="fade"
                mode="out-in"
            >
                <span
                    v-if="computedValue"
                    key="truthy"
                    class="ml-2.5 text-sm inline-block"
                >
                    <slot name="truthy">
                        Ja
                    </slot>
                </span>
                <span
                    v-else
                    key="falsy"
                    class="ml-2.5 text-sm inline-block"
                >
                    <slot name="falsy">
                        Nej
                    </slot>
                </span>
            </Transition>
        </div>
        <HelpText v-if="helpText">
            {{ helpText }}
        </HelpText>
    </div>
</template>
<script>
export default {
    name: 'FSwitch',
    props: {
        value: {
            type: [Number, Boolean, String],
            default: false
        },
        name: {
            type: String,
            default: 'switch'
        },
        columnLayout: {
            type: Boolean,
            default: false
        },
        helpText: {
            type: String,
            default: ''
        },
        disabled: {
            type: Boolean,
            default: false
        }
    },
    data () {
        return {
            newValue: this.value
        }
    },
    computed: {
        hasSlot () {
            return !!this.$slots.default
        },
        computedValue: {
            get () {
                return this.newValue
            },
            set (value) {
                this.newValue = value
                this.$emit('input', value)
            }
        },
        currentUserIsFirstIn() {
            return this.$store.getters['echo/currentUserIsFirstIn']
        },
        inputDisabled() {
            if(this.disabled) {
                return true
            }
            if(! this.currentUserIsFirstIn) {
                return true
            }
            return false
        },
    },
    watch: {
        /**
        * When v-model change, set internal value.
        */
        value (value) {
            this.newValue = value
        }
    },
    methods: {
        toggleSwitch () {
            if(this.inputDisabled) {
                return
            }
            this.computedValue = !this.computedValue
        }
    }
}
</script>
