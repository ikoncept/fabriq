<template>
    <div>
        <EventModal
            :event="activeEvent"
            :creating="creatingEvent"
            @updated="fetchEvents"
        />
        <UiSectionHeader class="mb-4">
            Kalender
            <template #tools>
                <button
                    class="block px-6 py-2.5 leading-none text-sm fabriq-btn btn-royal"
                    type="button"
                    @click="openCreateEventModal"
                >
                    Lägg till händelse
                </button>
            </template>
        </UiSectionHeader>
        <UiCard :padding="false">
            <div class="lg-cal">
                <!-- eslint-disable-next-line -->
                <v-calendar
                    class="max-w-full"
                    :masks="masks"
                    :attributes="events"
                    disable-page-swiped
                    is-expanded
                    @update:to-page="setCurrentMonthFilter"
                >
                    <template #header-left-button>
                        <ArrowRightLongIcon class="w-6 h-6 mx-5 text-gray-800 transform -rotate-180" />
                    </template>
                    <template #header-right-button>
                        <ArrowRightLongIcon class="w-6 h-6 mx-5 my-5 text-gray-800 transform rotate-0" />
                    </template>
                    <template #day-content="{ day, attributes }">
                        <div class="z-10 flex flex-col h-full overflow-hidden">
                            <span class="text-sm font-bold text-gray-400 day-label">{{ day.day }}</span>
                            <UiPopover
                                v-for="(attr, index) in attributes"
                                :key="day.id + index"
                                offset=""
                                class="w-full"
                                :class="attr.customData.class"
                                @show="fetchEvent(attr.customData.data.id)"
                            >
                                <template #popover>
                                    <div class="text-gray-800 bg-white border rounded-md shadow-lg w-96">
                                        <div class="">
                                            <div class="mb-4 border-b header">
                                                <div class="flex items-center justify-between px-6 py-5 font-bold">
                                                    <div class="pr-6">
                                                        {{ attr.customData.title }}
                                                    </div>
                                                    <div class="flex items-start space-x-4 text-gray-500">
                                                        <button
                                                            v-close-popover.all
                                                            class="focus:outline-none"
                                                            @click="editEvent(attr.customData.data)"
                                                        >
                                                            <PenToSquareIcon
                                                                thin
                                                                class="w-6 h-6"
                                                            />
                                                        </button>
                                                        <FConfirmDropdown
                                                            confirm-question="Vill du ta bort händelsen?"
                                                            @confirmed="deleteEvent(attr.customData.data.id)"
                                                        >
                                                            <TrashIcon
                                                                thin
                                                                class="w-6 h-6"
                                                            />
                                                        </FConfirmDropdown>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                v-if="Object.keys(eventData).length > 0"
                                                class="flex flex-col px-6 space-y-4 text-sm pb-7 body"
                                            >
                                                <p>
                                                    <span v-if="attr.customData.data.start != attr.customData.data.end">
                                                        {{ attr.customData.data.start | localTime('d MMMM yyyy') }} -
                                                        {{ attr.customData.data.end | localTime('d MMMM yyyy') }}
                                                    </span>
                                                    <span v-else>
                                                        {{ attr.customData.data.start | localTime('d MMMM yyyy') }}
                                                    </span>
                                                    <span v-if="attr.customData.data.start_time || attr.customData.data.end_time">{{ attr.customData.data.start_time }} - {{ attr.customData.data.end_time }}</span>
                                                </p>
                                                <p
                                                    v-if="eventData.content.data.location"
                                                    class="flex items-center"
                                                >
                                                    <LocationIcon class="inline-flex w-5 h-5 mr-1 text-gray-500" />
                                                    {{ eventData.content.data.location }}
                                                </p>
                                                <p
                                                    class="whitespace-pre-line"
                                                    v-text="eventData.content.data.description"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div class="flex flex-wrap w-full p-2 space-x-2 text-xs leading-tight text-white transition-colors duration-150 border rounded-md cursor-pointer bg-royal-500 hover:bg-royal-700">
                                    <div
                                        v-if="attr.customData.data.start_time "
                                        class="mr-2 time"
                                    >
                                        {{ attr.customData.data.start_time }}
                                    </div>
                                    <div class="flex-1 font-semibold">
                                        {{ attr.customData.title }}
                                    </div>
                                </div>
                            </UiPopover>
                        </div>
                    </template>
                </v-calendar>
            </div>
        </UiCard>
    </div>
</template>
<script>
import EventModal from '@/calendar/EventModal.vue'
import Event from '@/models/Event.js'
import { endOfMonth, format, startOfMonth } from 'date-fns'
export default {
    name: 'CalendarIndex',
    components: { EventModal },
    data () {
        return {
            calendarItems: [],
            pagination: {},
            show: {
                eventCreateModal: false
            },
            queryParams: {
                number: 300,
                page: 1,
                'filter[dateRange]': '1999-02-24,2021-03-04',
                append: 'title'
            },
            masks: {
                weekdays: 'WWWW'
            },
            eventsData: [],
            localesWithErrors: [],
            eventData: {},
            activeEvent: {},
            creatingEvent: true
        }
    },
    computed: {
        locales () {
            return this.$store.getters['config/supportedLocales']
        },
        events () {
            // Attributes for todos
            return [

                ...this.eventsData.map(eventItem => ({
                    dates: {
                        start: this.$options.filters.localTime(eventItem.start),
                        end: this.$options.filters.localTime(eventItem.end)
                        // end: eventItem.has_interval ? null : this.$options.filters.localTime(eventItem.end),
                        // dailyInterval: eventItem.daily_interval ? eventItem.daily_interval : 1
                        // months: 2,
                        // ordinalWeekdays: { 2: 1 }
                        // months: [1, 2, 3, 4, 5, 6]
                        // monthlyInterval: [1, 2, 3, 4, 5, 6, 7, 8]
                    },
                    customData: {
                        title: eventItem.title,
                        data: eventItem,
                        class: ''
                    }
                }))
                // {
                //     customData: {
                //         title: 'TEST',
                //         data: {},
                //         class: ''
                //     },
                //     // dates: { ordinalWeekdays: { 1: 3 } }
                //     dates: {
                //         start: this.$options.filters.localTime('2021-03-05 00:00:00'),
                //         dailyInterval: 7
                //         // end: this.$options.filters.localTime('2021-03-05 00:00:00')
                //         // weeklyInterval: 2
                //     }
                // }
            ]
        }

    },
    activated () {
        this.fetchEvents()
    },
    methods: {
        setCurrentMonthFilter (payload) {
            const start = startOfMonth(new Date(payload.year, payload.month - 1))
            const end = endOfMonth(new Date(payload.year, payload.month - 1))

            this.queryParams['filter[dateRange]'] = `${format(start, 'yyyy-MM-dd')},${format(end, 'yyyy-MM-dd')}`
            this.fetchEvents()
        },
        async fetchEvents () {
            try {
                const payload = {
                    params: this.queryParams
                }
                const { data } = await Event.index(payload)
                this.eventsData = data
            } catch (error) {
                console.error(error)
            }
        },
        async fetchEvent (id) {
            try {
                const payload = {
                    params: {
                        include: 'content,localizedContent'
                    }
                }
                const { data } = await Event.show(id, payload)
                this.eventData = data
            } catch (error) {
                console.error(error)
            }
        },
        async deleteEvent (id) {
            try {
                await Event.destroy(id)
                this.fetchEvents()
                this.$toast.success({ title: 'Händelsen har tagits bort' })
            } catch (error) {
                console.error(error)
            }
        },
        editEvent (event) {
            this.activeEvent = { ...this.eventData }
            this.creatingEvent = false
            this.$vfm.show('event-create-modal')
        },
        openCreateEventModal () {
            this.creatingEvent = true
            this.$vfm.show('event-create-modal')
        }
    }
}
</script>
<style>
</style>
