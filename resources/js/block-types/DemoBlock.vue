<template>
    <div>
        <div class="grid grid-cols-3 gap-x-6">
            <FInput v-model="localContent.name"
                    name="name"
                    label="Namn"
                    rules="required"
                    help-text="Visas endast internt"
            />
        </div>
        <hr class="w-full h-px my-6 ">
        <div class="grid grid-cols-12 mb-10 gap-x-6 gap-y-6">
            <FInput v-model="localContent.header"
                    name="header"
                    class="col-span-4"
                    label="Rubriktext"
            />
            <FInput v-model="localContent.subheader"
                    class="col-span-4"
                    name="subheader"
                    label="Underrubrik"
            />
            <FVideoInput v-model="localContent.video"
                         class="col-span-4"
                         name="video"
                         label="Video"
            />
        </div>
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-light">
                Kort
            </h3>
            <div v-show="localContent.children.length > 0"
                 class="flex justify-end"
            >
                <button
                    class="flex items-center text-sm link"
                    @click="addCard"
                >
                    <PlusIcon class="w-5 h-5 mr-2 " />Lägg till kort
                </button>
            </div>
        </div>
        <Draggable v-model="localContent.children"
                   handle=".handle"
                   tag="ul"
                   v-bind="dragOptions"
                   class="list-group"
                   :group="{ name: 'children', pull: 'clone', put: ['children'] }"
                   @start="drag = true"
                   @end="drag = false"
        >
            <UiCard v-for="(child, childIndex) in localContent.children"
                    :key="'child' + childIndex"
                    is-child
                    collapsible
                    class="mb-6"
                    no-shadow
                    header-classes="bg-gray-50 py-2"
            >
                <template #header>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1 space-x-6">
                            <GripVerticalIcon class="block w-4 h-4 text-gray-400 handle" />
                            <span class="inline-flex text-sm font-semibold text-gray-500">{{ child.name }}</span>
                        </div>
                        <div class="flex items-center">
                            <FConfirmDropdown confirm-question="Vill du ta bort detta kortet?"
                                              @confirmed="deleteChild(childIndex)"
                            >
                                <TrashIcon class="w-5 mt-1 duration-150 h-5transition-colors hover:text-red-500"
                                           thin
                                />
                            </FConfirmDropdown>
                            <div class="w-px h-8 mx-6 bg-gray-300" />
                        </div>
                    </div>
                </template>
                <div
                    class="grid grid-cols-3 gap-x-6 gap-y-6"
                >
                    <FInput v-model="child.name"
                            label="Namn"
                    />
                    <FSelect v-model="child.bgColor"
                             label="Bakgrundsfärg"
                             :reduce-fn="bgColor => bgColor.value"
                             name="bgColor"
                             value-key="value"
                             option-label="text"
                             :clearable="false"
                             :options="[{text: 'Grön', hex: '#2E604D', value: 'bg-moss-700'}, {text: 'Ingen', hex: '#fff', value: 'bg-white'},]"
                    >
                        <template #fop="option">
                            <div class="w-5 h-5 mr-4 border rounded-full"
                                 :style="{backgroundColor: option.hex}"
                            />
                            {{ option.text }}
                        </template>
                        <template #prefix="option">
                            <div class="cool">
                                <div class="w-5 h-5 mr-4 border rounded-full"
                                     :style="{backgroundColor: option.hex}"
                                />
                            </div>
                        </template>
                    </FSelect>
                    <div>
                        <FLabel>Bild</FLabel>
                        <div class="flex items-center mb-6">
                            <FSwitch v-model="child.hasImage" />
                            <div class="ml-2 text-sm"
                                 v-text="child.hasImage ? 'Ja' : 'Nej'"
                            />
                        </div>
                        <div v-if="child.hasImage">
                            <FImageInput
                                v-model="child.image"
                            />
                        </div>
                    </div>
                    <hr class="col-span-3 my-6">
                    <FInput v-model="child.header"
                            label="Rubriktext"
                            name="header"
                    />
                    <FInput v-model="child.subheader"
                            label="Underrubrik"
                            name="subheader"
                    />
                    <FEditor v-model="child.body"
                             label="Text"
                             class="col-span-3"
                    />
                    <div class="col-span-3">
                        <FButtonList v-model="child.buttons">
                            Knappar
                        </FButtonList>
                    </div>
                </div>
            </UiCard>
        </Draggable>

        <div v-show="localContent.children.length <= 0">
            <div
                class="grid mt-4 mb-6 sm:grid-cols-12 gap-x-6 gap-y-6"
            >
                <div class="flex flex-col col-span-5 mb-7 ">
                    <h4 class="mb-2 text-base font-light">
                        Inget kort har lagts till ännu
                    </h4>
                    <button
                        class="flex items-center text-sm font-semibold focus:outline-none"
                        type="button"
                        @click="addCard"
                    >
                        <PlusIcon class="w-5 h-5 mr-2 " />Lägg till kort
                    </button>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 mt-8">
            <div>
                <FLabel>
                    Blockknapp
                </FLabel>
                <div class="flex items-center">
                    <FSwitch v-model="localContent.hasButton"
                             class="h-10"
                    />
                    <div class="ml-2 text-sm"
                         v-text="localContent.hasButton ? 'Ja' : 'Nej'"
                    />
                </div>
            </div>
            <FButtonItem
                v-model="localContent.button"
                :disabled="! localContent.hasButton"
                class="col-span-12 lg:col-span-8"
            />
        </div>
    </div>
</template>
<script>
import Draggable from 'vuedraggable'
export default {
    name: 'CardBlock',
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
        this.$set(this.localContent, 'button', { text: '', linkType: 'internal', page_id: null })
    },
    methods: {
        addCard () {
            const newItem = {
                id: 'i' + Math.random().toString(20).substr(2, 6),
                name: 'Kort ' + (this.localContent.children.length + 1),
                newlyAdded: true
            }
            this.localContent.children.push(newItem)
            this.$nextTick(() => {
                newItem.newlyAdded = false
            })
        },
        deleteChild (index) {
            this.localContent.children.splice(index, 1)
        }
    }
}
</script>