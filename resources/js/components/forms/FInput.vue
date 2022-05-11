<template>
    <div>
        <ValidationProvider
            v-slot="{ errors, classes }"
            :mode="validationMode"
            :rules="rules"
        >
            <span :class="classes">
                <FLabel
                    v-if="label"
                    :name="name"
                    :non-white-bg="nonWhiteBg"
                    :optional="optional"

                    :required="hasRuleRequired"
                >
                    {{ label }}
                </FLabel>

                <div
                    v-if="inputType === 'radio'"
                    class="flex items-center space-x-4 min-h-10"
                >
                    <div
                        v-for="(radioOption, index) in options"
                        :key="'r' + index"
                        class="flex items-center"
                    >
                        <input
                            :id="id + radioOption.value + _uid"
                            :name="id + name + _uid"
                            type="radio"
                            :checked="radioOption.value == value"
                            :class="inputClasses"
                            :disabled="disabled"
                            class="w-5 h-5 fabriq-radio form-radio focus:outline-none focus:ring-offset-3 focus:ring-1 focus:ring-royal-300"
                            @input="updateValue(radioOption.value)"
                        >
                        <label
                            :for="id + radioOption.value + _uid"
                            class="cursor-pointer"
                        >
                            <slot
                                name="option"
                                v-bind="radioOption"
                            >
                                <span class="block ml-3 text-sm font-medium text-gray-700"> {{ radioOption.label }}</span>
                            </slot>
                        </label>
                    </div>
                </div>
                <textarea
                    v-else-if="type === 'textarea'"
                    ref="textarea"
                    :class="{classes: classes }"
                    :disabled="disabled"
                    :name="name"
                    :placeholder="placeholder"
                    :readonly="readOnly"
                    :value="value"
                    class=" flex-1 block w-full rounded px-4 py-2.5 text-sm antialiased text-gray-800 transition duration-200 ease-out appearance-none leadning-none ring-1 focus:outline-none ring-gray-300 focus:ring-gray-800"
                    :rows="rows"
                    v-on="inputListeners"
                    @input="updateValue($event.target.value)"
                />
                <span
                    v-else-if="inputType === 'input'"
                    class="relative flex items-center"
                >
                    <div
                        v-if="prefix"
                        class="inline-flex items-center px-3 py-2.5 text-sm font-medium text-gray-500 bg-gray-100 rounded-l ring-1 ring-gray-300"
                        v-text="prefix"
                    />
                    <span
                        class="absolute ml-4"
                        style="z-index: 1"
                    >
                        <slot name="icon" />
                    </span>
                    <input
                        ref="input"
                        v-mask="mask"
                        :class="[{classes: classes}, inputClasses, roundedClasses, hasIcon ? 'pl-12' : 'pl-4', disabled ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : 'text-gray-800 ']"
                        :disabled="disabled"
                        :min="min"
                        :name="name"
                        :placeholder="placeholder"
                        :readonly="readOnly"
                        :type="type"
                        :value="value"
                        class="relative flex-1 block w-full px-4 py-2.5 text-sm antialiased transition duration-200 ease-out appearance-none leadning-none ring-1 focus:outline-none ring-gray-300 focus:ring-gray-800"
                        @input="updateValue($event.target.value)"
                        v-on="inputListeners"
                    >
                    <div
                        v-if="suffix"
                        class="inline-flex items-center px-3 py-2.5 text-sm font-medium text-gray-500 bg-gray-100 rounded-r ring-1 ring-gray-300"
                        v-text="suffix"
                    />
                    <slot name="buttonSuffix" />
                </span>
                <HelpText v-if="helpText">
                    {{ helpText }}
                </HelpText>
                <span
                    v-if="errors[0]"
                    class="font-sans text-xs text-red-500"
                    :class="{'absolute': absolutePosErrors, 'hidden': hideErrors}"
                >
                    <span class="inline-flex items-center mt-2 leading-none">

                        <CircleExclamationIcon class="w-5 h-5 mr-2" />
                        {{ convertErrorMessage(errors[0]) }}</span>
                </span>
            </span>
        </ValidationProvider>
    </div>
</template>
<script>
import autosize from 'autosize'

export default {
    name: 'FInput',
    inheritAttrs: false,
    props: {
        value: {
            type: [String, Number, Object, Array, Boolean],
            default: ''
        },
        textarea: {
            type: Boolean,
            default: false
        },
        disabled: {
            type: Boolean,
            default: false
        },
        absolutePosErrors: {
            type: Boolean,
            default: false
        },
        hideErrors: {
            type: Boolean,
            default: false
        },
        name: {
            type: String,
            default: ''
        },
        label: {
            type: String,
            default: ''
        },
        type: {
            type: String,
            default: 'text'
        },
        inputType: {
            type: String,
            default: 'input'
        },
        options: {
            type: Array,
            default: () => {
                return []
            }
        },
        min: {
            type: String,
            default: ''
        },
        rules: {
            type: String,
            default: ''
        },
        question: {
            type: String,
            default: ''
        },
        placeholder: {
            type: String,
            default: ''
        },
        errorMessage: {
            type: String,
            default: 'Fältet är obligatoriskt'
        },
        readOnly: {
            type: Boolean,
            default: false
        },
        nonWhiteBg: {
            type: Boolean,
            default: false
        },
        helpText: {
            type: String,
            default: ''
        },
        mask: {
            type: String,
            default: ''
        },
        inputClasses: {
            type: String,
            default: ''
        },
        inputRef: {
            type: String,
            default: 'input'
        },
        validationMode: {
            type: String,
            default: 'eager'
        },
        suffix: {
            type: String,
            required: false,
            default: ''
        },
        prefix: {
            type: String,
            required: false,
            default: ''
        },
        optional: {
            type: String,
            default: ''
        },
        rows: {
            type: Number,
            default: 2
        },
        defaultValue: {
            type: [String, Number, Object, Array, Boolean],
            default: ''
        }
    },
    data () {
        return {
            id: ''
        }
    },
    computed: {
        hasIcon () {
            return !!this.$slots.icon
        },
        roundedClasses () {
            if (this.prefix && this.suffix) return 'rounded-none'
            if (this.prefix) return 'rounded-r'
            if (this.suffix || !!this.$slots.buttonSuffix) return 'rounded-l'

            return 'rounded'
        },
        hasRuleRequired () {
            return this.rules.includes('required')
        },
        inputListeners: function () {
            const vm = this
            // `Object.assign` merges objects together to form a new object
            return Object.assign({},
                // We add all the listeners from the parent
                this.$listeners,
                // Then we can add custom listeners or override the
                // behavior of some listeners.
                {
                    // This ensures that the component works with v-model
                    input: function (event) {
                        vm.$emit('input', event.target.value)
                    }
                }
            )
        }
    },
    mounted () {
        this.id = 'uid' + this._uid
        if (this.textarea) {
            this.$nextTick(() => {
                const area = this.$refs.textarea
                autosize(area)
            })
        }
        if (this.defaultValue) {
            console.log(this.defaultValue)
            this.updateValue(this.defaultValue)
        }
    },
    methods: {
        updateValue (value) {
            this.$emit('input', value)
        },
        convertErrorMessage (string) {
            if (string.includes('{field}')) {
                return string.replace('{field}', this.label.toLowerCase())
            }
            return string
        }
    }
}
</script>
<style scoped>

.form-radio:disabled {
    @apply  opacity-30 cursor-not-allowed ;
}
.form-radio:checked {
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16'  fill='%230b3b5b' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='4.5'/%3e%3c/svg%3e");
    @apply border border-gray-400;
    background-color: #fff;
    background-size: 100% 100%;
    background-position: center;
    background-repeat: no-repeat;
}

.form-radio {
    appearance: none;
    color-adjust: exact;
    user-select: none;
    flex-shrink: 0;
    border-radius: 100%;
    /* color: #0b3b5b; */
    border-color: #d1d5db;
    border-width: 1px;
    background-origin: border-box;
    background-size: 50% 50%;
    transition: background-size .1s ease;
}

</style>
