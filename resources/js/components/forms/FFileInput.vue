<template>
    <span>
        <FLabel
            v-if="label"
            :padding="false"
        >{{ label }}</FLabel>
        <div class="flex items-center space-x-4">

            <button
                class="px-6 py-2 text-sm cursor-pointer link fabriq-button btn-gold whitespace-nowrap"
                type="button"
                @click="pickerOpen = true"
            >
                Välj fil
            </button>
            <div v-if="! hasFile">
                <span class="text-sm italic text-gray-300">Ingen fil vald</span>
            </div>
            <div v-else>
                <div class="flex items-center space-x-4 text-sm ">
                    <div class="text-sm truncate max-w-32 xl:max-w-48">
                        {{ localFile.c_name }}
                    </div>
                    <div class="flex justify-start space-x-2">
                        <button
                            class="font-semibold text-left focus:outline-none"
                            @click="$vfm.show('file-modal', {id: localFile.id})"
                        >
                            <PenToSquareIcon class="w-4 h-4" />
                        </button>
                        <button
                            class="font-semibold focus:outline-none"
                            @click="clearFile"
                        >
                            <XMarkIcon class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <FMediaPicker
            :open="pickerOpen"
            media-type="file"
            @close="pickerOpen = false"
            @item-picked="pickFile"
        />
    </span>
</template>
<script>
import File from '~/models/File'
export default {
    name: 'FFileInput',
    props: {
        value: {
            type: [String, Number, Object, Array],
            default: null
        },
        label: {
            type: String,
            default: ''
        },
        modelId: {
            type: Number,
            required: false,
            default: 0
        },
        fieldKey: {
            type: String,
            required: false,
            default: ''
        },
        multiple: {
            type: Boolean,
            default: false
        },
        modelName: {
            type: String,
            default: ''
        },
        buttonText: {
            type: String,
            default: ''
        },
        group: {
            type: String,
            default: ''
        },
        syncSimilar: {
            type: Boolean,
            default: false
        }
    },
    data () {
        return {
            pickerOpen: false,
            localFile: null,
            isDraggingOver: false
        }
    },
    computed: {
        randomRef () {
            return Math.random().toString(20).substr(2, 6)
        },
        randomButtonRef () {
            return Math.random().toString(20).substr(2, 6)
        },
        hasFile () {
            return this.localFile.id
        }
    },
    created () {
        this.localFile = { ...this.value }
        this.$emit('input', this.localFile)
    },
    mounted () {
        // add the needed event listeners to the container

        // Listen on other group events
        if (this.group) {
            this.$eventBus.$on('file-selected', this.setFile)
        }
    },
    methods: {
        clearFile () {
            this.$emit('input', null)
            this.localFile = {}
            this.isDraggingOver = false
            this.emitFileSelected()
        },
        pickFile (id) {
            if (!this.multiple) {
                this.pickSingleFile(id)
            }
            if (this.multiple) {
                this.pickMultipleFiles(id)
            }
            this.isDraggingOver = false
        },
        async pickMultipleFiles (id) {
            try {
                const payload = {
                    model_id: this.modelId
                }
                const { data } = await File.attachToModel(id, this.modelName, payload)
                this.$emit('file-picked', data)
                this.pickerOpen = false
            } catch (error) {
                console.error(error)
            }
        },
        async pickSingleFile (id) {
            try {
                const payload = {
                    key: this.fieldKey,
                    model_id: this.modelId
                }
                const { data } = await File.show(id, payload)
                this.localFile = data
                this.$emit('input', data)
                this.emitFileSelected()
                this.pickerOpen = false
            } catch (error) {
                console.error(error)
            }
        },
        async handleNewFile (item) {
            await this.pickFile(item.id)
        },
        setFile (payload) {
            if (this.group === payload.group && this.randomRef !== payload.randomRef) {
                this.localFile = payload.data
                this.$emit('input', payload.data)
            }
        },
        emitFileSelected () {
            // If a group is specified we want to sync the input with
            // all other inputs in the same group. Useful when having multiple
            // inputs for a bunch of langugages and the input is not translated
            if (this.group) {
                this.$eventBus.$emit('file-selected', {
                    group: this.group,
                    data: { ...this.localFile },
                    randomRef: this.randomRef
                })
            }
        }
    }
}
</script>
