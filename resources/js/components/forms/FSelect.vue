<template>
    <div>
        <ValidationProvider v-slot="{ errors, classes }"
                            :mode="validationMode"
                            :rules="rules"
        >
            <FLabel v-if="label"
                    :class="classes"
                    :name="name"
                    :required="hasRuleRequired"
            >
                {{ label }}
            </FLabel>
            <VSelect
                :value="value"
                :options="options"
                :reduce="reduceFn"
                class="relative  flex-1 block w-full px-4 py-2.5 text-sm antialiased text-gray-800 transition duration-200 ease-out appearance-none leadning-none ring-1 focus:outline-none ring-gray-300 focus:ring-gray-800 rounded"
                :class="{'bg-gray-100 text-gray-500 cursor-not-allowed' : disabled}"
                :disabled="disabled"
                :label="optionLabel"
                :multiple="multiple"
                :name="name"
                :input-id="name"
                :clearable="clearable"
                :placeholder="placeholder"
                :taggable="taggable"
                :create-option="createOption"
                :push-tags="pushTags"
                @input="emitValue"
            >
                <template #selected-option="option">
                    <slot name="fop"
                          v-bind="option"
                    />
                </template>
                <template #option="option">
                    <div
                        class="inline-flex items-center"
                    >
                        <div v-if="value && valueKey && option[valueKey] === value"
                             class="absolute z-10 w-2 h-2 rounded-full bg-gold-500"
                        />
                        <div v-if="value && ! valueKey && option[optionLabel] === value[optionLabel]"
                             class="absolute w-2 h-2 rounded-full bg-gold-500"
                        />
                        <span class="flex items-center ml-4">
                            <slot
                                name="prefix"
                                v-bind="option"
                            />
                            <span v-text="option[optionLabel]" />
                        </span>
                    </div>
                </template>
                <template #no-options>
                    <div class="flex items-center justify-center px-2 space-x-3">
                        <span class="text-xl">🧐 </span> <span class="text-xs">Kunde inte hitta några resultat</span>
                    </div>
                </template>
                <template #open-indicator>
                    <AngleDownIcon class="w-4 h-4 text-gray-500 vs__open-indicator" />
                </template>
            </VSelect>
            <span v-if="errors[0]"
                  class="font-sans text-xs text-red-500"
                  :class="{'absolute': absolutePosErrors, 'hidden': hideErrors}"
            >
                <span class="inline-flex items-center mt-2 leading-none">

                    <CircleExclamationIcon class="w-5 h-5 mr-2" />
                    {{ convertErrorMessage(errors[0]) }}</span>
            </span>
            <p
                v-if="helpText"
                class="mt-2 font-sans text-xs italic text-gray-600"
                v-text="helpText"
            />
        </ValidationProvider>
    </div>
</template>

<script>
export default {
    name: 'FSelect',
    props: {
        value: {
            type: [String, Number, Object, Array],
            default: ''
        },
        options: {
            type: Array,
            required: false,
            default: () => []
        },
        label: {
            type: String,
            default: ''
        },
        optionLabel: {
            type: String,
            default: ''
        },
        valueKey: {
            type: String,
            default: ''
        },
        multiple: {
            type: Boolean,
            default: false
        },
        placeholder: {
            type: String,
            default: 'Välj'
        },
        clearable: {
            type: Boolean,
            default: true
        },
        reduceFn: {
            type: Function,
            required: false,
            default: item => item.name
        },
        rules: {
            type: String,
            default: ''
        },
        validationMode: {
            type: String,
            default: 'eager'
        },
        name: {
            type: String,
            required: true
        },
        absolutePosErrors: {
            type: Boolean,
            default: false
        },
        hideErrors: {
            type: Boolean,
            default: false
        },
        taggable: {
            type: Boolean,
            default: false
        },
        pushTags: {
            type: Boolean,
            default: false
        },
        createOption: {
            type: Function,
            default: () => {}
        },
        disabled: {
            type: Boolean,
            default: false
        },
        helpText: {
            type: String,
            default: ''
        },
        defaultValue: {
            type: [String, Number, Object, Array],
            default: ''
        }
    },
    computed: {
        hasRuleRequired () {
            return this.rules.includes('required')
        }
    },
    mounted () {
        if (this.defaultValue && !this.value) {
            this.$emit('input', this.defaultValue)
        }
    },
    methods: {
        emitValue (value) {
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
