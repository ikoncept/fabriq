<template>
    <div>
        <div class="flex justify-between mt-2 mb-4 text-sm font-semibold">
            <div class="text-xl font-light">
                <slot />
            </div>
            <button
                :disabled="addLocked"
                :class="{'cursor-not-allowed text-neutral-400': addLocked}"
                class="flex items-center text-sm font-semibold focus:outline-none"
                type="button"
                @click="addButton"
            >
                <span
                    v-if="maxItems && maxItems <= buttons.length"
                    class="mr-4 text-xs italic font-normal text-neutral-400"
                >Du har nått det maximala antalet knappar </span><PlusIcon class="w-5 h-5 mr-2 " />Lägg till knapp
            </button>
        </div>
        <div v-if="noButtons">
            <UiDashedBox size="min-h-24">
                <template #header>
                    <div class="text-base">
                        Ingen knapp har lagts till ännu
                    </div>
                </template>
                <template #link>
                    <div class="flex justify-center">
                        <button
                            v-if="noButtons"
                            class="flex items-center text-sm font-semibold focus:outline-none"
                            type="button"
                            @click="addButton"
                        >
                            <PlusIcon class="w-5 h-5 mr-2 " />Lägg till knapp
                        </button>
                    </div>
                </template>
            </UiDashedBox>
        </div>
        <div
            v-for="(button, index) in buttons"
            :key="index"
            class="flex mb-6 space-x-6"
        >
            <FButtonItem
                v-model="buttons[index]"
                class="flex-1 col-span-12"
                :placeholder="placeholder"
                :pages="pages"
            />
            <div
                v-if="mergedOptions.newTab"
                class="col-span-2"
            >
                <FLabel>
                    <span class="flex items-center">

                        Öppna i ny flik? <ExternalLinkIcon class="w-4 h-4 ml-2" />
                    </span>
                </FLabel>
                <div class="flex items-center mt-3">
                    <FSwitch v-model="button.newTab" />
                    <div
                        class="ml-2 text-sm"
                        v-text="button.newTab ? 'Ja' : 'Nej'"
                    />
                </div>
            </div>
            <div class="flex items-end col-span-1 spliceButton">
                <button
                    type="button"
                    class="p-4 -m-4 transition-colors duration-200 transform focus:outline-none hover:text-red-600"
                    @click="spliceButton(index)"
                >
                    <MinusIcon class="w-8 h-8 mb-2" />
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import PageTree from '@/models/PageTree.js'
import Menu from '@/models/Menu.js'
export default {
    name: 'FButtonList',
    props: {
        value: {
            type: Array,
            default: () => []
        },
        options: {
            required: false,
            type: Object,
            default: () => ({})
        },
        maxItems: {
            type: Number,
            required: false,
            default: null
        },
        placeholder: {
            type: String,
            required: false,
            default: ''
        }
    },
    data () {
        return {
            buttons: [],
            defaultOptions: {
                newTab: false
            },
            pages: []
        }
    },
    computed: {
        noButtons () {
            return this.buttons.length === 0
        },
        mergedOptions () {
            return {
                ...this.defaultOptions,
                ...this.options
            }
        },
        addLocked() {
            return this.maxItems && this.maxItems <= this.buttons.length
        }
    },
    created () {
        if (!this.value) {
            this.$emit('input', this.buttons)
        }
        this.fetchPages()
        this.buttons = this.value
    },
    methods: {
        async fetchPages () {
            try {
                const payload = {
                    params: {
                        // field: 'id,name',
                        append: 'paths',
                        selectOptions: true
                    }
                }
                const { data } = await PageTree.index(payload)
                this.pages = data
            } catch (error) {
                console.error(error)
            }
        },
        async fetchTree () {
            try {
                const payload = {
                    params: {
                        // field: 'id,name',
                        selectOptions: true
                    }
                }
                const { data } = await Menu.showTree(1, payload)
                this.pages = data
            } catch (error) {
                console.error(error)
            }
        },
        addButton () {
            this.buttons.push({
                type: 'link',
                linkType: 'internal',
                text: '',
                url: '',
                newTab: false,
                file: {
                    id: 0
                }
            })
            this.$emit('input', this.buttons)

            this.fetchPages()
            // this.fetchTree()
        },
        spliceButton (index) {
            this.buttons.splice(index, 1)
        }
    }
}
</script>
