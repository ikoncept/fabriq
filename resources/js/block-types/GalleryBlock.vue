<template>
    <div>
        <div class="grid grid-cols-3 gap-x-6">
            <FInput
                v-model="localContent.name"
                name="name"
                label="Namn"
                rules="required"
                help-text="Visas endast internt"
            />
            <FSelect
                v-model="localContent.columnSize"
                label="Bildstorlek"
                class="col-span-1"
                default-value="m"
                :reduce-fn="size => size.value"
                name="columnSize"
                value-key="value"
                option-label="text"
                :clearable="false"
                :options="[{ text: 'Large', value: 'grid-cols-1'}, { text: 'Medium', value: 'grid-cols-2' }, { text: 'Small', value: 'grid-cols-4' }, { text: 'Extra Small', value: 'grid-cols-6' }]"
            />
        </div>
        <hr class="w-full h-px my-6 ">
        <div class="grid grid-cols-12 mb-10 gap-x-6 gap-y-6">
            <FInput
                v-model="localContent.header"
                name="header"
                class="col-span-4"
                label="Rubriktext"
            />
        </div>
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <h3 class="text-xl font-light ">
                    Bilder
                </h3>
            </div>
            <div
                v-show="localContent.children.length > 0"
                class="flex justify-end"
            >
                <span>
                    <button
                        class="flex items-center text-sm link"
                        @click="addCard"
                    >

                        <PlusIcon class="w-5 h-5 mr-2 " />Lägg till bild
                    </button>
                </span>
            </div>
        </div>
        <div class="">
            <Draggable
                v-model="localContent.children"
                handle=".handle"
                tag="ul"
                v-bind="dragOptions"
                class="grid grid-cols-1 mt-4 list-group gap-x-6 gap-y-6 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4"
                :group="{ name: 'images', pull: false, put: false }"
                @start="drag = true"
                @end="drag = false"
            >
                <div
                    v-for="(child, childIndex) in localContent.children"
                    :key="'child' + childIndex"
                >
                    <div class="p-3 bg-gray-50">
                        <div class="flex items-center justify-between mb-2 text-sm">
                            <div class="flex items-center">
                                <GripVerticalIcon class="block w-4 h-4 mr-2 text-gray-400 cursor-move handle" /> Bild {{ childIndex + 1 }}
                            </div>
                            <div class="relative flex items-center space-x-4">
                                <div
                                    v-if="child.hasOwnProperty('link')"
                                    class="flex items-center"
                                >
                                    <input
                                        v-model="child.link"
                                        class="py-0.5 px-2 rounded text-xs focus:outline-none focus:ring-gray-400 focus:ring-1"
                                        placeholder="https://exempel.se"
                                    >
                                    <div>
                                        <button
                                            class="focus:outline-none"
                                            @click="disableLink(childIndex)"
                                        >
                                            <XMarkIcon class="absolute top-0 right-0 w-4 h-4 mt-1 mr-10 text-gray-400 transition-colors duration-150" />
                                        </button>
                                    </div>
                                </div>
                                <button
                                    v-else
                                    class="focus:outline-none"
                                    @click="enableLink(childIndex)"
                                >
                                    <LinkIcon class="w-5 h-5 transition-colors duration-150" />
                                </button>
                                <FConfirmDropdown
                                    confirm-question="Vill du ta bort bilden?"
                                    @confirmed="deleteChild(childIndex)"
                                >
                                    <TrashIcon class="w-4 h-4 mt-1 transition-colors duration-150 hover:text-red-500" />
                                </FConfirmDropdown>
                            </div>
                        </div>
                        <div class="ml-6">
                            <FImageInput
                                :key="'i'+ child.id"
                                v-model="child.image"
                            />
                        </div>
                    </div>
                </div>
            </Draggable>
        </div>

        <div v-show="localContent.children.length <= 0">
            <div
                class="grid mt-4 mb-6 sm:grid-cols-12 gap-x-6 gap-y-6"
            >
                <div class="flex flex-col col-span-5 mb-7 ">
                    <h4 class="mb-2 text-base font-light">
                        Inga bilder har lagts till ännu
                    </h4>
                    <button
                        class="flex items-center text-sm font-semibold focus:outline-none"
                        type="button"
                        @click="addCard"
                    >
                        <PlusIcon class="w-5 h-5 mr-2 " />Lägg till bild
                    </button>
                </div>
            </div>
        </div>
        <div class="hidden col-span-3">
            <div class="flex space-x-12">
                <div>
                    <FLabel>Blockknapp</FLabel>
                    <div class="flex items-center mt-3">
                        <FSwitch v-model="localContent.hasButton" />
                        <div
                            class="ml-2 text-sm"
                            v-text="localContent.hasButton ? 'Ja' : 'Nej'"
                        />
                    </div>
                </div>
                <FInput
                    v-model="localContent.buttonText"
                    :disabled="! localContent.hasButton"
                    class="w-72"
                    label="Knapptext"
                />
                <FInput
                    v-model="localContent.buttonUrl"
                    :disabled="! localContent.hasButton"
                    class="w-72"
                    label="Länkadress"
                />
            </div>
        </div>
    </div>
</template>
<script>
import Draggable from 'vuedraggable'
export default {
    name: 'GalleryBlock',
    components: { Draggable },
    props: {
        index: {
            type: Number,
            default: 0
        },
        value: {
            type: [String, Number, Object, Array],
            default: () => {
                return {
                    name: '',
                    header: ''
                }
            }
        },
        content: {
            type: [Array, Object],
            required: true,
            default: () => {
                return {
                    name: '',
                    headerType: {
                        name: ''
                    }
                    // repeaters: []
                }
            }
        }
    },
    data () {
        return {
            headerTypes: [
                {
                    text: 'Heading 1',
                    value: 'h1'
                },
                {
                    text: 'Heading 2',
                    value: 'h2'
                },
                {
                    text: 'Heading 3',
                    value: 'h3'
                }
            ],
            // repeaters: this.content.repeaters,
            localContent: this.content
        }
    },
    computed: {
        dragOptions () {
            return {
                animation: 200,
                group: 'description',
                disabled: false,
                ghostClass: 'ghost'
            }
        }
    },
    mounted () {
        // if (!this.content.repeaters) {
        //     this.localContent.repeaters = []
        // }
    },
    methods: {
        addCard () {
            const newItem = {
                id: 'i' + Math.random().toString(20).substr(2, 6),
                name: 'Bild ' + (this.localContent.children.length + 1),
                newlyAdded: true,
                hidden: false
            }
            this.localContent.children.push(newItem)
            this.$nextTick(() => {
                newItem.newlyAdded = false
            })
        },
        deleteChild (index) {
            const clone = { ...this.localContent }
            clone.children.splice(index, 1)
            this.localContent = {
                children: []
            }
            this.$nextTick(() => {
                this.localContent = clone
            })
        },
        enableLink (index) {
            this.$set(this.localContent.children[index], 'link', '')
        },
        disableLink (index) {
            this.$delete(this.localContent.children[index], 'link')
        }
    }
}
</script>
