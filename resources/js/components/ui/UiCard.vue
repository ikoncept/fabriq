<template>
    <div class="mt-4 bg-white rounded card"
         :class="noShadow ? 'border' : 'shadow-md'"
    >
        <div
            v-if="hasHeaderSlot"
            :class="[{'px-6': padding }, {'cursor-pointer flex justify-between': collapsible }, collapsible && collapsedActive ? 'border-gray-200' : 'border-transparent', ! open && collapsible ? ' rounded-b' : 'bg-white ', cHeaderClasses, collapsible ? 'border-transparent' : 'border-gray-200']"
            class="flex items-center leading-none transition-colors duration-500 bg-white border-b rounded-t"
            @click="toggleCollapsible"
        >
            <span class="flex-1 text-xl font-light text-gray-700">
                <slot name="header" />
            </span>
            <div v-if="collapsible">
                <AngleDownIcon class="block w-6 h-6 ml-4 text-gray-800 transition-all duration-300 transform "
                               :class="open ? '-rotate-180' : 'rotate-0'"
                />
            </div>
        </div>
        <div v-if="! collapsible"
             :class="{'p-6': padding }"
        >
            <slot />
        </div>
        <SlideUpDown v-else
                     ref="monkey"
                     :active="open"
                     :duration="animationDuration"
                     class="wobbly-accordion"
                     @open-start="delayOpen = true"
                     @close-end="delayOpen = false"
        >
            <div v-if="delayOpen"
                 :class="{'p-6': padding }"
            >
                <slot />
            </div>
        </SlideUpDown>
    </div>
</template>
<script>
export default {
    name: 'UiCard',
    props: {
        padding: {
            type: Boolean,
            default: true
        },
        collapsible: {
            type: Boolean,
            default: false
        },
        headerClasses: {
            type: String,
            default: ''
        },
        noShadow: {
            type: Boolean,
            default: false
        },
        isChild: {
            type: Boolean,
            default: false
        },
        group: {
            type: String,
            default: ''
        },
        syncGroups: {
            type: Boolean,
            default: false
        },
        openByDefault: {
            type: Boolean,
            default: false
        }
    },
    data () {
        return {
            open: false,
            delayOpen: false,
            animationDuration: 500
        }
    },
    computed: {
        hasHeaderSlot () {
            return !!this.$slots.header
        },
        collapsedActive () {
            return this.open && this.collapsible
        },
        cHeaderClasses () {
            if (this.headerClasses) {
                return this.headerClasses
            }
            return 'py-4'
        }
    },

    beforeDestroy () {
        this.$eventBus.$off('open-all-cards', this.openCollapsible)
        this.$eventBus.$off('close-all-cards', this.closeCollapsible)
        this.$eventBus.$off('open-synced-groups', this.matchGroupAndOpen)
        this.$eventBus.$off('close-synced-groups', this.matchGroupAndClose)
    },
    mounted () {
        if (!this.isChild) {
            this.$eventBus.$on('open-all-cards', this.openCollapsible)
            this.$eventBus.$on('close-all-cards', this.closeCollapsible)
            this.$eventBus.$on('relayout-cards', this.relayoutCard)
        }
        if (this.syncGroups) {
            this.$eventBus.$on('open-synced-groups', this.matchGroupAndOpen)
            this.$eventBus.$on('close-synced-groups', this.matchGroupAndClose)
        }

        if (this.collapsible && this.openByDefault) {
            this.open = true
        }
    },
    methods: {
        relayoutCard () {
            if (this.$refs.monkey) {
                this.animationDuration = 0
                this.$refs.monkey.layout()
                setTimeout(() => {
                    this.animationDuration = 500
                }, 100)
            }
        },
        matchGroupAndOpen (parameters) {
            if (this.group === parameters) {
                this.openCollapsible(false)
            }
        },
        matchGroupAndClose (parameters) {
            if (this.group === parameters) {
                this.closeCollapsible(false)
            }
        },
        openCollapsible (emit = true) {
            this.$emit('before-open')
            if (this.syncGroups) {
                if (emit) {
                    this.$eventBus.$emit('open-synced-groups', this.group)
                }
            }
            this.open = true
        },
        closeCollapsible (emit = true) {
            this.$emit('before-close')
            if (this.syncGroups) {
                if (emit) {
                    this.$eventBus.$emit('close-synced-groups', this.group)
                }
            }
            this.open = false
        },
        toggleCollapsible () {
            if (this.open) {
                this.closeCollapsible()
                return
            }
            this.openCollapsible()
        }
    }
}
</script>
