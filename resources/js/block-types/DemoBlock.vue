<template>
    <div>
        <div class="grid grid-cols-3 gap-x-6">
            <FInput v-model="localContent.name"
                    name="name"
                    label="Namn"
                    rules="required"
                    help-text="Visas endast internt"
            />
            <FSelect v-model="localContent.size"
                     name="size"
                     default-value="large"
                     label="Storlek"
                     :options="[{ label: 'Small', value: 'small' }, { label: 'Medium', value: 'medium' }, { label: 'Large', value: 'large' }]"
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
            <FImageInput v-model="localContent.image"
                         class="col-span-4"
                         label="Bild"
                         name="image"
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
                        <div class="flex items-center space-x-4">
                            <button v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Klona block' }"
                                    class="focus:outline-none"
                                    @click.stop="addCard(child)"
                            >
                                <CloneIcon
                                    thin
                                    class="h-6"
                                />
                            </button>
                            <button v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Kopiera block-ID' }"
                                    v-clipboard="'#' + child.id"
                                    v-clipboard:success="copySuccess"
                                    class="focus:outline-none"
                                    type="button"
                                    @click.stop
                            >
                                <LinkIcon class="h-6"
                                          thin
                                />
                            </button>
                            <FButtonSwitch v-model="child.hidden"
                                           class="self-center mb-1 "
                            />
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
        addCard (item) {
            let newItem = {}
            if (!item.name) {
                newItem = {
                    id: 'i' + Math.random().toString(20).substr(2, 6),
                    name: 'Kort ' + (this.localContent.children.length + 1),
                    newlyAdded: true
                }
            } else {
                newItem = { ...item }
                newItem.id = 'i' + Math.random().toString(20).substr(2, 6)
                newItem.name = 'Kopia av ' + newItem.name
            }
            this.localContent.children.push(newItem)
            this.$nextTick(() => {
                newItem.newlyAdded = false
            })
        },
        deleteChild (index) {
            this.localContent.children.splice(index, 1)
        },
        copySuccess () {
            this.$toast.success({ title: 'Kortets ID har kopierats', message: 'Klista in som en extern länk i fältet till kontrollen du önskar länka blocket till.' })
        }
    }
}
</script>
