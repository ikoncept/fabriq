<template>
    <FModal
        v-model="show"
        name="block-type-modal"
        :click-to-close="false"
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
                    class="grid grid-cols-1 gap-x-4 sm:grid-cols-2"
                    @submit.prevent="setBlockType"
                >
                    <FSelect
                        ref="selectInput"
                        v-model="chosenBlock.block_type"
                        label="Blocktyp"
                        :clearable="false"
                        name="block_type"
                        rules="required"
                        :options="blockTypes"
                        :reduce-fn="block_type => block_type"
                        option-label="name"
                    />
                    <FInput
                        ref="nameInput"
                        v-model="chosenBlock.name"
                        name="name"
                        rules="required"
                        label="Namn"
                    />
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
import Page from '~/models/BlockType'
function defaultCreationObject () {
    return {
        name: '',
        block_type: null
    }
}
export default {
    name: 'BlockTypeModal',
    props: {
        item: {
            type: Object,
            default: () => {}
        }

    },
    data () {
        return {
            show: false,
            blockTypes: [],
            chosenBlock: {
                name: '',
                block_type: null
            }
        }
    },
    methods: {
        async fetchBlockTypes () {
            try {
                const payload = {
                    params: {
                        field: 'id,name,component_name,has_children'
                    }
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
            this.$eventBus.$emit('block-type-added', this.chosenBlock)

            this.show = false
        },
        resetCreateModal () {
            this.chosenBlock = { ...defaultCreationObject() }
            this.$nextTick(() => {
                this.$refs.observer.reset()
            })
        }
    }
}
</script>
