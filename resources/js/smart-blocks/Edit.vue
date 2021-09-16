<template>
    <div>
        <div>
            <UiSectionHeader>
                Redigera smart block
                <template #subtitle>
                    {{ smartBlock.name }}
                </template>
                <template #tools>
                    <div class="space-x-4 whitespace-nowrap">
                        <FButton
                            class="px-6 py-2.5 leading-none text-sm fabriq-btn btn-link"
                            back-button="smartBlocks.index"
                        >
                            Avbryt
                        </FButton>
                        <FButton
                            :click="updateContent"
                            class="px-6 py-2.5 leading-none text-sm fabriq-btn btn-royal"
                        >
                            Spara
                        </FButton>
                    </div>
                </template>
            </UiSectionHeader>
        </div>
        <FTabs v-if="Object.keys(locales).length"
               @change="setLanguage"
        >
            <FTab v-for="(locale, lIndex) in locales"
                  :key="lIndex"
                  :value-key="lIndex"
                  :title="locale.native"
            >
                <UiCard
                    group="smartBlocks-ettings"
                    sync-groups
                >
                    <template #header>
                        Inställningar
                    </template>
                    <div class="grid grid-cols-3 gap-x-6 gap-y-6">
                        <FInput v-model="smartBlock.name"
                                label="Namn"
                                help-text="Visas endast internt"
                        />
                    </div>
                </UiCard>
                <div>
                    <div class="flex items-center justify-between mt-12 mb-4">
                        <h4 class="text-3xl font-light text-gray-700">
                            Block
                        </h4>
                        <button class="flex items-center text-sm link"
                                @click="showBlockTypeModal"
                        >
                            <PlusIcon class="w-5 h-5 mr-2" />Lägg till block
                        </button>
                    </div>
                    <div v-if="Object.keys(localizedContent).length">
                        <div v-if="! localizedContent[activeLocale].boxes.length">
                            <div class="flex items-center justify-center h-48 border-2 border-dashed rounded border-royal-200">
                                <div class="flex flex-col items-center">
                                    <div class="mb-4 text-xl font-light">
                                        Inga block har lagts till ännu
                                    </div>
                                    <button class="flex items-center text-sm link"
                                            @click="showBlockTypeModal"
                                    >
                                        <PlusIcon class="w-5 h-5 mr-2" />Lägg till block
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-else>
                            <Draggable v-model="localizedContent[activeLocale].boxes"
                                       handle=".handle"
                                       tag="ul"
                                       v-bind="dragOptions"
                                       class="list-group"
                                       @start="drag = true"
                                       @end="drag = false"
                            >
                                <TransitionGroup type="transition"
                                                 :name="'flip-list-move'"
                                >
                                    <li
                                        v-for="(block, boxIndex) in localizedContent[activeLocale].boxes"
                                        :key="'alt' + boxIndex + activeLocale"
                                        class="list-group-item"
                                    >
                                        <UiCard v-if="block.name"
                                                collapsible
                                                :open-by-default="block.newlyAdded"
                                        >
                                            <template #header>
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center flex-1 space-x-6">
                                                        <GripVerticalIcon class="block w-6 h-6 text-gray-300 cursor-move handle" />
                                                        <div class="flex items-end space-x-6">
                                                            <div class="leading-none">
                                                                {{ block.name }}
                                                            </div>
                                                            <span class="inline-flex text-sm font-semibold leading-none text-gray-400">{{ block.block_type.name }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <!-- <ellipsis-icon class="w-6 h-6 mr-4" /> -->
                                                        <button v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Kopiera block-ID' }"
                                                                v-clipboard="'#' + block.id"
                                                                v-clipboard:success="copySuccess"
                                                                class="focus:outline-none"
                                                                type="button"
                                                                @click.stop
                                                        >
                                                            <LinkIcon class="h-6 mr-4"
                                                                      thin
                                                            />
                                                        </button>
                                                        <FButtonSwitch v-model="block.hidden"
                                                                       class="self-center mb-1 mr-4 "
                                                        />
                                                        <FConfirmDropdown confirm-question="Vill du ta bort detta blocket?"
                                                                          class="relative w-6 h-6"
                                                                          @confirmed="deleteBlock(boxIndex)"
                                                        >
                                                            <TrashIcon class="h-6 transition-colors duration-150 hover:text-red-500"
                                                                       thin
                                                            />
                                                        </FConfirmDropdown>
                                                        <div class="w-px h-8 mx-6 bg-gray-300" />
                                                    </div>
                                                </div>
                                            </template>
                                            <KeepAlive>
                                                <Component
                                                    :is="block.block_type.component_name"
                                                    :content.sync="localizedContent[activeLocale].boxes[boxIndex]"
                                                    :value="block"
                                                    :index="boxIndex"
                                                    @input="block = $event.target.value"
                                                    @repeater-updated="refreshBlock"
                                                />
                                            </KeepAlive>
                                        </UiCard>
                                    </li>
                                </TransitionGroup>
                            </Draggable>
                        </div>
                    </div>
                </div>
            </FTab>
        </FTabs>
    </div>
</template>
<script>
import SmartBlock from '~/models/SmartBlock'
import Draggable from 'vuedraggable'
export default {
    name: 'SmartBlocksEdit',
    components: { Draggable },
    beforeRouteLeave (from, to, next) {
        this.$vfm.hide('block-type-modal')
        this.$eventBus.$off('block-type-added', this.blockTypeAdded)
        this.$destroy()
        next()
    },
    data () {
        return {
            active: false,
            id: 0,
            queryParams: {
                include: 'localizedContent',
                locale: 'all',
                append: 'paths'
            },
            smartBlock: {
                id: 0,
                updated_at: '2020-01-01 10:00:00',
                localizedContent: {
                    sv: {}
                },
                template: {
                    data: {}
                }
            },
            content: {},
            drag: false,
            activeLocale: 'sv',
            localizedContent: {}
        }
    },
    computed: {
        config () {
            return this.$store.getters['config/config']
        },
        dragOptions () {
            return {
                animation: 200,
                group: 'description',
                disabled: false,
                ghostClass: 'ghost'
            }
        },
        locales () {
            return this.$store.getters['config/supportedLocales']
        }
    },
    activated () {
        this.id = this.$route.params.id
        this.$eventBus.$on('block-type-added', this.blockTypeAdded)
        this.fetchSmartBlock()
    },
    methods: {
        openAllCards () {
            this.$eventBus.$emit('open-all-cards')
        },
        async updateContent () {
            try {
                const payload = {
                    name: this.smartBlock.name,
                    localizedContent: { ...this.localizedContent }
                }
                await SmartBlock.update(this.id, payload)
            } catch (error) {
                console.error(error)
            }
        },
        async fetchSmartBlock () {
            const payload = {
                params: this.queryParams
            }
            try {
                this.smartBlock.id = 0
                const { data } = await SmartBlock.show(this.id, payload)
                this.smartBlock = data
                const localizedContent = { ...data.localizedContent.data }
                Object.keys(localizedContent).forEach((item) => {
                    // if (!localizedContent[item].content.boxes) {
                    // }
                    let thing = {}
                    if (!localizedContent[item].content.boxes) {
                        thing.boxes = []
                    } else {
                        thing = localizedContent[item].content
                    }
                    this.$set(this.localizedContent, item, { ...thing })
                })
            } catch (error) {
                console.error(error)
            }
        },
        setLanguage (key) {
            this.activeLocale = key
            this.$eventBus.$emit('relayout-cards')
        },
        showBlockTypeModal () {
            this.$vfm.show('block-type-modal')
        },
        blockTypeAdded (item) {
            if (!this.localizedContent[this.activeLocale].boxes) {
                this.$set(this.localizedContent[this.activeLocale], 'boxes', [])
            }
            this.localizedContent[this.activeLocale].boxes.push({ ...item })
            this.$nextTick(() => {
                this.localizedContent[this.activeLocale].boxes[this.localizedContent[this.activeLocale].boxes.length - 1].newlyAdded = false
            })
        },
        deleteBlock (index) {
            // this.content.boxes.splice(index, 1)
            this.localizedContent[this.activeLocale].boxes.splice(index, 1)
        },
        refreshBlock (payload) {
            this.localizedContent[this.activeLocale].boxes[payload.index] = { ...payload.item }
            console.log(payload.item)
        },
        copySuccess () {
            this.$toast.success({ title: 'Blockets ID har kopierats', message: 'Klista in som en extern länk i fältet till kontrollen du önskar länka blocket till.' })
        }
    }
}
</script>
