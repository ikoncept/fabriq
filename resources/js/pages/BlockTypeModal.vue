<template>
    <FModal
        v-model="show"
        name="block-type-modal"
        :click-to-close="false"
        overflow="overflow-y-auto"
        :width="! largeBlockPicker ? 'max-w-3xl' : 'max-w-6xl'"
        @before-open="fetchBlockTypes"
        @closed="resetCreateModal"
    >
        <template #title>
            <span
                class="text-gray-700 fd"
                v-text="'Lägg till block'"
            />
        </template>
        <template #actions>
            <div class="flex justify-end space-x-4">
                <button
                    class="px-8 py-2.5 leading-none fabriq-btn btn-link"
                    @click="show = false"
                >
                    Stäng
                </button>
                <FButton
                    without-loader
                    :click="setBlockType"
                    class="px-8 py-2.5 leading-none fabriq-btn btn-royal"
                >
                    Lägg till
                </FButton>
            </div>
        </template>
        <div class="relative z-40 py-2">
            <ValidationObserver ref="observer">
                <form
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2"
                    @submit.prevent="setBlockType"
                >
                    <FSelect
                        v-if="!largeBlockPicker"
                        ref="selectInput"
                        v-model="chosenBlock.block_type"
                        label="Blocktyp"
                        :clearable="false"
                        name="block_type"
                        rules="required"
                        :options="blockTypes"
                        :reduce-fn="block_type => block_type"
                        option-label="name"
                        @input="selectBlock"
                    />
                    <FInput
                        v-if="largeBlockPicker"
                        v-model="chosenBlock.block_type.name"
                        name="block_type"
                        rules="required|min:2"
                        class="hidden"
                        label="Blocktyp"
                    />
                    <FInput
                        ref="nameInput"
                        v-model="chosenBlock.name"
                        name="name"
                        rules="required"
                        label="Namn"
                    />
                    <div
                        v-if="largeBlockPicker"
                        class="grid grid-cols-4 col-span-2 gap-4"
                    >
                        <div
                            v-show="recommendedBlockTypes.length > 0"
                            class="col-span-4 -mb-4"
                        >
                            <FLabel>Rekommenderade block</FLabel>
                        </div>
                        <div
                            v-for="blockType in recommendedBlockTypes"
                            :key="blockType.id"
                            :class="blockType.id === chosenBlock.block_type.id ? '  border-royal-500  bg-royal-50' : 'opacity-60 border-white'"
                            class="flex flex-col items-center p-3 pb-1 transition-all duration-200 border rounded-md cursor-pointer"
                            @click="selectBlock(blockType)"
                        >
                            <img
                                :src="`data:image/svg+xml;base64,` + blockType.base_64_svg"
                                class="w-full"
                                alt=""
                            >
                            <div class="mt-2 text-xs">
                                {{ blockType.name }} !!
                            </div>
                        </div>
                        <div class="col-span-4 -mb-4">
                            <FLabel v-text="recommendedBlockTypes.length > 0 ? 'Övriga' : 'Välj blocktyp'" />
                        </div>
                        <!-- <pre>{{ recommendedBlockTypes }}</pre> -->
                        <div
                            v-for="blockType in computedBlockTypes"
                            :key="blockType.id"
                            :class="blockType.id === chosenBlock.block_type.id ? '  border-royal-500  bg-royal-50' : 'opacity-60 border-white'"
                            class="flex flex-col items-center p-3 pb-1 transition-all duration-200 border rounded-md cursor-pointer"
                            @click="selectBlock(blockType)"
                        >
                            <img
                                :src="`data:image/svg+xml;base64,` + blockType.base_64_svg"
                                class="w-full"
                                alt=""
                            >
                            <div class="mt-2 text-xs">
                                {{ blockType.name }}
                            </div>
                        </div>
                    </div>
                    <button
                        class="hidden"
                        type="submit"
                        :click="setBlockType"
                    />
                </form>
            </ValidationObserver>
        </div>
    </FModal>
</template>

<script>
import Page from '@/models/BlockType.js'

function defaultCreationObject () {
    return {
        id: 0,
        name: '',
        block_type: {
            id: 0,
            name: 'Välj blocktyp',
        },
    }
}

export default {
    name: 'BlockTypeModal',
    props: {
        item: {
            type: Object,
            default: () => {},
        },

    },

    data () {
        return {
            show: false,
            blockTypes: [],
            chosenBlock: {
                id: 0,
                name: '',
                block_type: {
                    name: '',
                    id: 0,
                },
            },
        }
    },

    computed: {
        computedBlockTypes() {
            const firstFilter = this.blockTypes.filter(item => {

                // If visible_for is defined and template slug is not present
                if (item.options.visible_for && ! item.options.visible_for.includes(this.page.template.data.slug)) {
                    return false
                }

                if (! item.options.hidden_for) {
                    return true
                }

                if (item.options.hidden_for.includes(this.page.template.data.slug)) {
                    return false
                }

                if (item.options.recommended_for.includes(this.page.template.data.slug)) {
                    return false
                }
                return true
            })

            return firstFilter
        },
        recommendedBlockTypes() {
            return this.blockTypes.filter(item => {
                return item.options.recommended_for.includes(this.page.template.data.slug)
            })
        },
        largeBlockPicker() {
            if(! this.config.ui) {
                return false
            }

            return !!this.config.ui.large_block_picker
        },

        config() {
            return this.$store.getters['config/config']
        },

        blockNames() {
            return this.blockTypes.map(item => {
                return item.name
            })
        },

        activeLocale() {
            return this.$store.getters['config/activeLocale']
        },
        page() {
            return this.$store.getters['page/page']
        }
    },

    methods: {
        async fetchBlockTypes () {
            try {
                const payload = {
                    params: {
                        field: 'id,name,component_name,has_children,base_64_svg,options',
                    },
                }
                const { data } = await Page.index(payload)

                this.blockTypes = data
            } catch (error) {
                console.error(error)
            }
        },

        async setBlockType () {
            const isValid = await this.$refs.observer.validate()

            if (!isValid) {
                return
            }

            if (this.chosenBlock.block_type.has_children) {
                this.chosenBlock.children = []
            }

            this.chosenBlock.newlyAdded = true
            this.chosenBlock.id = 'i' + Math.random().toString(20).substr(2, 6)
            const emitName = 'block-type-added-' + this.activeLocale


            this.$eventBus.$emit(emitName, this.chosenBlock)

            setTimeout(() => {
                this.$store.commit('ui/toggleOpenCard', this.chosenBlock.id)
            }, 150);

            this.show = false
        },

        resetCreateModal () {
            this.chosenBlock = { ...defaultCreationObject() }
            this.$nextTick(() => {
                this.$refs.observer.reset()
            })
        },

        selectBlock(blockType) {
            this.chosenBlock.block_type = blockType

            if (!this.chosenBlock.name || this.blockNames.includes(this.chosenBlock.name)) {
                this.chosenBlock.name = blockType.name
            }
        },
    },
}
</script>
