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
                @click="addFile"
            >
                <span
                    v-if="maxItems && maxItems <= files.length"
                    class="mr-4 text-xs italic font-normal text-neutral-400"
                >Du har nått det maximala antalet filer </span>
                <PlusIcon class="w-5 h-5 mr-2 " />Lägg till fil
            </button>
        </div>
        <div v-if="noFiles">
            <UiDashedBox size="min-h-24">
                <template #header>
                    <div class="text-base">
                        Ingen fil har lagts till ännu
                    </div>
                </template>
                <template #link>
                    <div class="flex justify-center">
                        <button
                            v-if="noFiles"
                            class="flex items-center text-sm font-semibold focus:outline-none"
                            type="button"
                            @click="addFile"
                        >
                            <PlusIcon class="w-5 h-5 mr-2 " />Lägg till fil
                        </button>
                    </div>
                </template>
            </UiDashedBox>
        </div>
        <Draggable
            v-model="files"
            handle=".handle"
            tag="div"
            v-bind="dragOptions"
            class="list-group border rounded"
            :group="{ name: 'files', pull: 'clone', put: ['files'] }"
            @start="drag = true"
            @end="emitChange"
        >
            <div
                v-for="(file, index) in files"
                :key="file._id"
                class="flex gap-x-4 items-center px-4 py-1.5 border-b last-of-type:border-b-0"
            >
                <div class="cursor-move">
                    <GripVerticalIcon class="block w-4 h-4 text-gray-400 handle" />
                </div>
                <FFileInput
                    v-model="files[index]"
                    class="flex-1 col-span-12"
                    :placeholder="placeholder"
                    :pages="pages"
                    :color="color"
                    :arrow="arrow"
                />
                <div class="flex items-end col-span-1 spliceFile">
                    <button
                        type="button"
                        class="p-4 -m-4 transition-colors duration-200 transform focus:outline-none hover:text-red-600"
                        @click="spliceFile(index)"
                    >
                        <MinusIcon
                            class="w-6 h-6"
                            thin
                        />
                    </button>
                </div>
            </div>
        </Draggable>
    </div>
</template>
<script>
import Draggable from 'vuedraggable'

export default {
    name: 'FFileList',
    components: {
        Draggable
    },
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
        },
        color: {
            type: Boolean,
            default: false,
        },
        arrow: {
            type: Boolean,
            default: false,
        }
    },
    data () {
        return {
            files: [],
            defaultOptions: {
                newTab: false
            },
            pages: []
        }
    },
    computed: {
        noFiles () {
            return this.files.length === 0
        },
        mergedOptions () {
            return {
                ...this.defaultOptions,
                ...this.options
            }
        },
        addLocked() {
            return this.maxItems && this.maxItems <= this.files.length
        },
        dragOptions () {
            return {
                animation: 200,
                group: 'description',
                disabled: false,
                ghostClass: 'ghost'
            }
        }
    },
    created () {
        if (!this.value) {
            this.$emit('input', this.files)
        }
        this.files = this.value
        // Ensure existing files have unique IDs
        this.files.forEach(file => {
            if (!file._id) {
                file._id = Math.random().toString(20).substring(2, 8)
            }
        })
    },
    methods: {
        emitChange() {

            this.$emit('input', this.files)
        },
        addFile () {
            this.files.push({
                _id: Math.random().toString(20).substring(2, 8),
                type:'internal',
                title: '',
                url: '',
                newTab: false,
                file: {
                    id: null
                }
            })
            this.$emit('input', this.files)

            this.fetchPages()
            // this.fetchTree()
        },
        spliceFile (index) {
            this.files.splice(index, 1)
        }
    }
}
</script>
