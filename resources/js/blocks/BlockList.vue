<template>
    <div>
        <div class="flex items-center justify-between mt-12 mb-4">
            <h4 class="text-3xl font-light text-gray-700">
                Block
            </h4>
            <button
                v-show="!lockedBlocks"
                class="flex items-center text-sm link"
                @click="showBlockTypeModal"
            >
                <PlusIcon class="w-5 h-5 mr-2" />Lägg till block
            </button>
        </div>
        <div>
            <div v-if="localBlocks.length === 0">
                <div class="flex items-center justify-center h-48 border-2 border-dashed rounded border-royal-200">
                    <div class="flex flex-col items-center">
                        <div class="mb-4 text-xl font-light">
                            Inga block har lagts till ännu
                        </div>
                        <button
                            class="flex items-center text-sm link"
                            @click="showBlockTypeModal"
                        >
                            <PlusIcon class="w-5 h-5 mr-2" />Lägg till block
                        </button>
                    </div>
                </div>
            </div>

            <Draggable
                v-model="localBlocks"
                handle=".handle"
                tag="ul"
                v-bind="dragOptions"
                class="list-group"
                @start="drag = true"
                @end="drag = false"
            >
                <TransitionGroup
                    type="transition"
                    :name="'flip-list-move'"
                >
                    <li
                        v-for="(block, boxIndex) in localBlocks"
                        :key="'alt' + boxIndex + activeLocale"
                        class="list-group-item"
                    >
                        <UiCard
                            v-if="block.id"
                            collapsible
                            :identifier="block.id"
                            :open-by-default="block.newlyAdded"
                        >
                            <template #header>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center flex-1 space-x-6">
                                        <GripVerticalIcon
                                            v-if="!lockedBlocks"
                                            class="block w-6 h-6 text-gray-300 cursor-move handle"
                                        />
                                        <div class="flex items-end space-x-6">
                                            <div class="leading-none">
                                                {{ block.name }}
                                            </div>
                                            <span class="inline-flex text-sm font-semibold leading-none text-gray-400">{{ block.block_type.name }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <!-- <ellipsis-icon class="w-6 h-6 mr-4" /> -->
                                        <div v-if="lockedBlocks">
                                            <LockIcon
                                                class="h-6"
                                            />
                                        </div>
                                        <div
                                            v-if="lockedBlocks"
                                            class="w-px h-8 mx-6 bg-gray-300"
                                        />
                                        <button
                                            v-show="!lockedBlocks"
                                            v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Klona block' }"
                                            class="focus:outline-none"
                                            @click.stop="cloneBlock(block)"
                                        >
                                            <CloneIcon
                                                thin
                                                class="h-6"
                                            />
                                        </button>
                                        <button
                                            v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Kopiera block-ID' }"
                                            v-clipboard="'#' + block.id"
                                            v-clipboard:success="copySuccess"
                                            class="focus:outline-none"
                                            type="button"
                                            @click.stop
                                        >
                                            <LinkIcon
                                                class="h-6"
                                                thin
                                            />
                                        </button>
                                        <FButtonSwitch
                                            v-model="block.hidden"
                                            class="self-center mb-1 "
                                        />
                                        <FConfirmDropdown
                                            v-show="!lockedBlocks"
                                            confirm-question="Vill du ta bort detta blocket?"
                                            class="relative w-6 h-6"
                                            @confirmed="deleteBlock(boxIndex)"
                                        >
                                            <TrashIcon
                                                class="h-6 transition-colors duration-150 hover:text-red-500"
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
                                    :content="block"
                                    :value="block"
                                    :index="boxIndex"
                                    @input="blockEdited"
                                    @repeater-updated="refreshBlock"
                                />
                            </KeepAlive>
                        </UiCard>
                    </li>
                </TransitionGroup>
            </Draggable>
        </div>
    </div>
</template>

<script>
import Draggable from 'vuedraggable'

export default {
    components: {
        Draggable,
    },

    props: {
        value: {
            type: [Array],
            default: () => [],
        },

        page: {
            type: Object,
            default: () => {
                return {
                    locked: false,
                }
            },
        },

        locale: {
            type: String,
            required: true,
            default: 'sv',
        },
    },

    emits: ['input'],


    computed: {

        localBlocks: {
            get() {
                return this.value
            },

            set(value) {
                this.$emit('input', value)
            },
        },

        config () {
            return this.$store.getters['config/config']
        },

        dragOptions () {
            return {
                animation: 200,
                group: 'description',
                disabled: this.lockedBlocks,
                ghostClass: 'ghost',
            }
        },

        lockedBlocks() {
            if(this.devMode) {
                return false
            }

            return this.page.locked
        },

        devMode () {
            return this.$store.getters['config/devMode']
        },

        currentUserIsFirstIn() {
            return this.$store.getters['echo/currentUserIsFirstIn']
        },

        activeLocale() {
            return this.$store.getters['config/activeLocale']
        },

    },


    mounted() {
        console.log('adding event for block type added', 'block-type-added-' + this.locale)
        this.$eventBus.$on('block-type-added-' + this.locale, this.blockTypeAdded)
    },

    beforeUnmount() {
        this.$eventBus.$off('block-type-added-' + this.locale, this.blockTypeAdded)
        // this.$eventBus.$off('block-type-added', this.blockTypeAdded)
    },

    methods: {

        copySuccess () {
            this.$toast.success({ title: 'Blockets ID har kopierats',
                message: 'Klista in som en extern länk i fältet till kontrollen du önskar länka blocket till.' })
        },

        refreshBlock (payload) {
            this.$emit('input', this.localBlocks)
        },

        showBlockTypeModal () {
            this.$vfm.show('block-type-modal')
        },

        deleteBlock (index) {
            this.localBlocks.splice(index, 1)
        },

        blockTypeAdded (item) {
            console.warn('adding new block', item)

            if (this.localBlocks.length === 0) {
                this.localBlocks = []
            }

            this.$nextTick(() => {
                this.localBlocks.push(JSON.parse(JSON.stringify(item)))
            });

            setTimeout(() => {
                this.localBlocks[this.localBlocks.length - 1].newlyAdded = false
            }, 2);
        },

        cloneBlock (block) {
            const clonedBlock = JSON.parse(JSON.stringify(block))

            clonedBlock.id = 'i' + Math.random().toString(20).substr(2, 6)
            clonedBlock.name = 'Kopia av ' + block.name
            this.blockTypeAdded({ ...clonedBlock })
        },

        blockEdited(value) {
            console.log('asd')
        },
    },
}
</script>

<style>
.flip-list-move {
  transition: transform 0.5s;
}
.no-move {
  transition: transform 0s;
}
.ghost {
  opacity: 0.5;
  /* background: #c8ebfb; */
}
.flip-list-move {
    transition: transform 0.5s;
}
.no-move {
    transition: transform 0s;
}
.ghost,
.sortable-ghost {
    opacity: 0.5;
}
.list-group-item i {
    cursor: pointer;
}
</style>
