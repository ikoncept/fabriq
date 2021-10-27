<template>
    <div
        class="flex items-start w-full md:items-end"
    >
        <div ref="container"
             class="mr-4 text-xl font-light md:text-4xl"
        >
            <slot />
        </div>
        <div class="text-sm font-semibold text-gray-400 div-b">
            <slot name="subtitle" />
        </div>
        <Transition name="fade" v-if="hasToolsSlot">
            <div v-if="!showFixedTools"
                    key="fixedTools"

                    class="ml-auto div-c"
            >
                <slot name="tools" />
            </div>
            <div v-else
                    key="nonfixedTools"
                    class="fixed z-50 p-2.5 ml-auto bg-white rounded shadow-md right-8 div-c fixed-tools"
            >
                <slot name="tools" />
            </div>
        </Transition>
    </div>
</template>
<script>
export default {
    name: 'UiSectionHeader',
    data () {
        return {
            observer: null,
            showFixedTools: false
        }
    },
    computed: {
        hasItemSlot () {
            return !!this.$slots.item
        },
        hasToolsSlot () {
            return !!this.$slots.tools
        }
    },
    mounted () {
        this.observer.observe(this.$refs.container)
    },
    created () {
        this.observer = new IntersectionObserver(
            this.onElementObserved,
            {
                root: this.$refs.container,
                threshold: 1.0
            }
        )
    },
    beforeDestroy () {
        this.observer.disconnect()
    },
    methods: {
        onElementObserved (entries) {
            entries.forEach(({ target, isIntersecting }) => {
                if (!isIntersecting) {
                    this.showFixedTools = true
                    return
                }

                this.showFixedTools = false
            })
        }
    }
}
</script>
<style>
.div-b {
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
  margin-right: 5px;
  width: 0;
  flex-grow: 1;
  max-width: -moz-max-content;
  max-width: -webkit-max-content;
  max-width: max-content;
}
.div-c {
  white-space: nowrap;
}
</style>
