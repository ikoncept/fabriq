<template>
    <FModal
        v-model="localShow"
        name="event-create-modal"

        width="max-w-2xl"
        :overflow="overflow"
        @before-open="prepareData"
        @closed="resetCreateModal"
    >
        <template #title>
            <span v-text="creating ? 'Lägg till händelse' : 'Redigera händelse'" />
        </template>
        <div>
            <ValidationObserver ref="observer">
                <FDatePicker
                    v-model="createEvent.date"
                    label="Datum"
                    mode="date"
                    name="date"
                    is-range
                    rules="required"
                    clearable
                    placeholder="Klicka för att välja datum"
                    class="block col-span-8 mt-2 mb-6"
                />
                <div class="grid grid-cols-2 gap-6 mb-4">
                    <FInput
                        v-model="createEvent.start_time"
                        name="start_time"
                        mask="##:##"
                        label="Tid"
                        type="text"
                    >
                        <template #icon>
                            <ClockIcon class="w-4 h-4" />
                        </template>
                    </FInput>
                    <div>
                        <FLabel name="end_time">
&nbsp;
                        </FLabel>
                        <FInput
                            v-model="createEvent.end_time"
                            mask="##:##"
                            name="end_time"
                            type="text"
                        >
                            <template #icon>
                                <ClockIcon class="w-4 h-4" />
                            </template>
                        </FInput>
                    </div>
                    <div class="col-span-2">
                        <FInput
                            v-model="createEvent.daily_interval"
                            input-type="radio"
                            name="daily_interval"
                            label="Repetera händelse"
                            :options="[{label: 'Nej', value: 0}, { label: 'Varje vecka', value: 7 }, { label: 'Varannan vecka', value: 14 }]"
                        />
                    </div>
                </div>
            </ValidationObserver>
            <FTabs v-if="Object.keys(locales).length > 0">
                <FTab
                    v-for="(locale, key) in locales"
                    :key="locale.regional"
                    :has-error="localesWithErrors.includes(key)"
                    :title="locale.native"
                >
                    <ValidationObserver
                        v-if="Object.keys(locales).length > 0"
                        :ref="'createForm' + key"
                        v-slot="{ passes }"
                    >
                        <form
                            v-if="createEvent[key]"
                            @submit.prevent="passes(storeEvent)"
                        >
                            <div class="grid grid-cols-12 mt-4 gap-x-6 gap-y-8">
                                <FInput
                                    v-model="createEvent[key].title"
                                    label="Titel"
                                    placeholder="Ange en titel"
                                    class="col-span-5"
                                    :rules="key === 'sv' ? 'required' : ''"
                                    name="titel"
                                />
                                <FInput
                                    v-model="createEvent[key].location"
                                    label="Plats"
                                    placeholder="Ange plats"
                                    class="col-span-7"
                                    name="location"
                                >
                                    <template #icon>
                                        <LocationIcon class="w-5 h-5 text-gray-400" />
                                    </template>
                                </FInput>

                                <FInput
                                    v-model="createEvent[key].description"
                                    class="col-span-12 mb-6"
                                    name="description"
                                    label="Beskrivning "
                                    placeholder="Beskriv händelsen"
                                    textarea
                                />
                                <button
                                    type="submit"
                                    class="hidden"
                                />
                            </div>
                        </form>
                    </ValidationObserver>
                </FTab>
            </FTabs>
        </div>
        <template #actions>
            <div class="flex justify-end space-x-4">
                <div class="flex space-x-4">
                    <button
                        class="px-8 py-2.5 leading-none fabriq-btn btn-link"
                        @click="localShow = false"
                    >
                        Avbryt
                    </button>
                    <FButton
                        :click="storeEvent"
                        class="px-8 py-2.5 leading-none fabriq-btn btn-royal"
                    >
                        Spara
                    </FButton>
                </div>
            </div>
        </template>
    </FModal>
</template>
<script>
import Event from '@/models/Event.js'
function defaultCreationObject () {
    return {
        title: '',
        location: '',
        description: ''
    }
}
function defaultMainCreationObject () {
    return {
        date: '',
        start_time: '',
        end_time: '',
        daily_interval: 0
    }
}
export default {
    name: 'EventModal',
    props: {
        show: {
            type: Boolean,
            default: false
        },
        creating: {
            type: Boolean,
            default: true
        },
        event: {
            type: Object,
            default: () => {
                return {}
            }
        }
    },
    data () {
        return {
            localShow: false,
            createEvent: {
                date: '',
                start_time: '',
                end_time: '',
                daily_interval: 0
            },
            overflow: 'overflow-y-auto',
            localesWithErrors: []

        }
    },
    computed: {
        locales () {
            return this.$store.getters['config/supportedLocales']
        }
    },
    methods: {
        prepareData () {
            if (!this.creating) {
                this.createEvent = { ...this.event }
                this.createEvent.date = {
                    start: this.createEvent.start,
                    end: this.createEvent.end
                }
                const localizedContent = { ...this.event.localizedContent.data }
                Object.keys(localizedContent).forEach((locale, key) => {
                    this.$set(this.createEvent, locale, { ...localizedContent[locale].content })
                })
            } else {
                Object.keys(this.locales).forEach(locale => {
                    this.createEvent = { ...defaultMainCreationObject() }
                    this.$nextTick(() => {
                        this.$set(this.createEvent, locale, { ...defaultCreationObject() })
                    })
                })
            }
        },
        resetCreateModal () {
            this.$nextTick(() => {
                // this.$o
                Object.keys(this.locales).forEach(locale => {
                    // this.$refs['createForm' + locale].reset()
                    this.$refs['createForm' + locale][0].reset()
                })
                // createForm
                this.$refs.observer.reset()
            })
        },
        async storeEvent () {
            this.localesWithErrors = []
            const result = await this.validateForms()
            if (!result) {
                this.$toast.warning({ title: 'Oj då!', message: 'Något är tosigt med datan du försöker skicka.' })
                return
            }
            try {
                const localizedContent = {}
                Object.keys(this.locales).forEach(locale => {
                    localizedContent[locale] = this.createEvent[locale]
                })
                const payload = {
                    date: {
                        start: this.createEvent.date.start,
                        end: this.createEvent.date.end
                    },
                    start_time: this.createEvent.start_time,
                    end_time: this.createEvent.end_time,
                    daily_interval: this.createEvent.daily_interval,
                    localizedContent: localizedContent
                }
                if (this.creating) {
                    await Event.store(payload)
                    this.$toast.success({ title: 'Händelsen har skapats! ' })
                    this.$emit('updated')
                } else {
                    await Event.update(this.event.id, payload)
                    this.$toast.success({ title: 'Händelsen har uppdaterats!' })
                    this.$emit('updated')
                }
                this.localShow = false
            } catch (error) {
                console.error(error)
            }
        },
        async validateForms () {
            // const mainIsValid = false
            const formsToValidate = [
                this.$refs.observer.validate()
            ]
            Object.keys(this.locales).forEach(locale => {
                this.$refs['createForm' + locale][0].validate().then(result => {
                    if (!result) {
                        this.localesWithErrors.push(locale)
                    }
                })
            })
            Object.keys(this.locales).forEach(locale => {
                formsToValidate.push(this.$refs['createForm' + locale][0].validate())
            })

            const result = await Promise.all(formsToValidate)
            if (!result.includes(false)) {
                return true
            }

            return false
        }
    }
}
</script>
