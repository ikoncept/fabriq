<template>
    <div class="flex items-center">
        <span v-if="hasSlot"
              :name="name"
              class="flex flex-col mr-4 "
              @click="computedValue = ! computedValue"
        >
            <FLabel class="mb-0 cursor-pointer select-none"
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
            @click="computedValue = ! computedValue"
        >
            <span class="sr-only">Use setting</span>
            <!-- On: "translate-x-5", Off: "translate-x-0" -->
            <span
                :class="{'translate-x-5': computedValue, 'translate-x-0':! computedValue}"
                aria-hidden="true"
                class="absolute top-0 left-0 inline-block w-5 h-5 transition duration-200 ease-in-out transform bg-white rounded-full shadow ring-0"
            />
        </button>
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
        }
    },
    watch: {
        /**
        * When v-model change, set internal value.
        */
        value (value) {
            this.newValue = value
        }
    }
}
</script>
