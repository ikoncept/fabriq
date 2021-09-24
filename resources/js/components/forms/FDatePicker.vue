<template>
    <ValidationProvider v-slot="{ errors, classes }"
                        :mode="validationMode"
                        :rules="rules"
    >
        <VDatePicker
            :class="classes"
            :timezone="localTimezone"
            :value="value"
            :popover="{visibility: 'click' }"
            color="gray"
            :select-attribute="selectAttribute"
            :is-range="isRange"
            :mode="mode"
            is24hr
            @input="updateValue"
        >
            <template v-if="isRange"
                      #default="{ inputValue, inputEvents, isDragging }"
            >
                <div class="flex justify-between">
                    <FLabel v-if="label"
                            class="mb-1"
                            :name="name"
                            :required="hasRuleRequired"
                    >
                        {{ label }}
                    </FLabel>
                    <button class="text-xs link"
                            @click="clearDates"
                    >
                        Rensa
                    </button>
                </div>
                <div class="flex flex-col items-center justify-start sm:flex-row">
                    <div class="relative flex-grow">
                        <FInput :class="isDragging ? 'text-gray-600' : 'text-gray-900'"
                                :value="inputValue.start"
                                :placeholder="placeholder"
                                v-on="inputEvents.start"
                        >
                            <template #icon>
                                <CalendarIcon class="w-4 h-4" />
                            </template>
                        </FInput>
                    </div>
                    <span class="flex-shrink-0 m-2">
                        <MinusIcon thin
                                   class="w-6 h-6 text-gray-800"
                        />
                    </span>
                    <div class="relative flex-grow">
                        <FInput :class="isDragging ? 'text-gray-600' : 'text-gray-900'"
                                :value="inputValue.end"
                                v-on="inputEvents.end"
                        >
                            <template #icon>
                                <CalendarIcon class="w-4 h-4" />
                            </template>
                        </FInput>
                    </div>
                </div>
            </template>
            <template v-else
                      #default="{ inputValue, inputEvents }"
            >
                <span class="flex justify-between">

                    <FLabel v-if="label"
                            class="mb-1"
                            :name="name"
                            :required="hasRuleRequired"
                    >
                        {{ label }}
                    </FLabel>
                    <button v-if="clearable"
                            class="text-xs link"
                            type="button"
                            @click="clearDates"
                    >
                        Rensa
                    </button>
                </span>
                <FInput
                    class=""
                    :value="inputValue"
                    :placeholder="placeholder"
                    v-on="inputEvents"
                >
                    <template #icon>
                        <CalendarIcon class="w-4 h-4" />
                    </template>
                </FInput>
            </template>
        </VDatePicker>
        <span v-if="errors[0]"
              class="font-sans text-xs text-red-500"
              :class="{'absolute': absolutePosErrors, 'hidden': hideErrors}"
        >
            <span class="inline-flex items-center mt-2 leading-none">

                <CircleExclamationIcon class="w-5 h-5 mr-2" />
                {{ convertErrorMessage(errors[0]) }}</span>
        </span>
    </ValidationProvider>
</template>
<script>
import { format } from 'date-fns'
export default {
    name: 'FDatePicker',
    props: {
        value: {
            required: true,
            type: [Object, Array, String, Date, Number]
        },
        isRange: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            default: ''
        },
        formatFn: {
            type: Function,
            default: (date) => format(date, 'yyyy-MM-dd HH:mm')
        },
        clearable: {
            type: Boolean,
            default: false
        },
        mode: {
            type: String,
            default: 'date'
        },
        timezone: {
            type: String,
            default: 'Europe/Stockholm'
        },
        placeholder: {
            type: String,
            default: ''
        },
        validationMode: {
            type: String,
            default: 'eager'
        },
        absolutePosErrors: {
            type: Boolean,
            default: false
        },
        hideErrors: {
            type: Boolean,
            default: false
        },
        rules: {
            type: String,
            default: ''
        },
        name: {
            type: String,
            default: ''
        }
    },
    data () {
        return {
            range: {
                start: new Date(2020, 0, 6),
                end: new Date(2020, 0, 23)
            },
            localTimezone: 'UTC',
            // timezone: 'UTC',
            // masks: {
            //     input: 'YYYY-MM-DD',
            //     output: 'YYYY-MM-DD 00:00:00'
            // },
            selectAttribute: {
            }
        }
    },
    computed: {
        hasRuleRequired () {
            return this.rules.includes('required')
        }
    },
    mounted () {
        this.localTimezone = this.timezone
    },
    methods: {
        clearDates () {
            let reset = ''
            if (this.isRange) {
                reset = {
                    start: null,
                    end: null
                }
            }
            this.$emit('input', reset)
            this.$nextTick(() => {
                this.$emit('updated')
            })
        },
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