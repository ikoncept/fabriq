<template>
    <div>
        <ul
            v-show="showTabs && ! hideTabs"
            class="flex -mb-px space-x-8 border-b border-gray-200"
        >
            <li
                v-for="(tab, index) in tabs"
                :key="tab.title"
                :class="index == selectedIndex ? 'border-gold-700 text-gray-800' : 'text-gray-400 hover:text-gray-600 hover:border-gray-300'"
                class="px-1 py-4 text-sm font-medium transition-colors duration-200 border-b-2 border-transparent cursor-pointer whitespace-nowrap"
                @click="selectTab(index)"
            >
                <div class="flex items-center">
                    <CircleExclamationIcon
                        v-if="tab.hasError"
                        class="w-5 h-5 mr-2 text-red-500"
                    />
                    {{ tab.title }}
                </div>
            </li>
        </ul>
        <slot />
    </div>
</template>
<script>
export default {
    name: 'FTabs',
    props: {
        showWhenSingle: {
            type: Boolean,
            default: false
        },
        hideTabs: {
            type: Boolean,
            default: false
        },
        identifier: {
            type: String,
            default: Math.random().toString(20).substr(2, 6)
        }
    },
    data () {
        return {
            selectedIndex: 0, // the index of the selected tab,
            tabs: [] // all of the tabs,
        }
    },
    computed: {
        showTabs () {
            if (this.tabs.length === 1) {
                if (this.showWhenSingle) {
                    return true
                }

                return false
            }

            return true
        }
    },
    created () {
        this.tabs = this.$children
    },
    mounted () {
        this.selectTab(0)
        this.$eventBus.$on('set-active-tab', this.handleActiveTabEvent)
    },
    beforeDestroy () {
        this.$eventBus.$off('set-active-tab', this.handleActiveTabEvent)
    },
    methods: {
        handleActiveTabEvent (payload) {
            if (payload.identifier !== this.identifier) {
                return
            }
            this.selectTab(payload.index)
        },
        selectTab (index_) {
            this.selectedIndex = index_

            this.$emit('change', this.tabs[index_].$props.valueKey)

            // loop over all the tabs
            this.tabs.forEach((tab, index) => {
                tab.isActive = (index === index_)
            })
        }
    }
}
</script>
