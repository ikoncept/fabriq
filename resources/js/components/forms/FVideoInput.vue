<template>
    <span>
        <FLabel v-if="label"
                :padding="false"
        >{{ label }}</FLabel>
        <div :ref="randomRef"

             class="relative flex items-center justify-center transition-colors duration-150 border-dashed rounded-md aspect-w-16 aspect-h-9"
             :class="[hasVideo ? 'border-transparent ring-2 ring-inset' : 'ring-royal-500  ring-2 ring-inset', isDraggingOver ? 'bg-royal-100' : 'bg-royal-50']"
        >
            <div v-show=" !hasVideo"
                 class="absolute flex flex-col items-center justify-center "
            >
                <div
                    class="flex flex-col items-center space-y-2 text-sm "
                >
                    <div>
                        <button
                            :ref="randomButtonRef"
                            type="button"
                            class="inline-flex px-6 py-2.5 mb-2 text-sm leading-none fabriq-btn btn-royal"
                        >Ladda upp video</button>
                    </div>
                    eller
                    <button class="cursor-pointer link"
                            type="button"
                            @click="pickerOpen = true"
                    >
                        välj från arkivet
                    </button>
                </div>
                <FUpload
                    class="absolute pt-4 pb-1 mx-auto text-sm "
                    :max-items="1"
                    endpoint="/api/admin/uploads/videos"
                    types="video/mp4,video/mov,video/quicktime,video/webm"
                    upload-name="video"
                    :drop-ref="randomRef"
                    :button-ref="randomButtonRef"
                    without-button
                    @upload-complete="handleNewVideo"
                />
            </div>
            <div v-if="hasVideo"
                 class="absolute w-full h-full group"
            >
                <div class="absolute inset-0 z-10 flex items-end justify-end transition-opacity duration-300 opacity-0 group-hover:opacity-100 ">
                    <div class="flex w-full -mb-px">
                        <button class="flex items-center justify-center w-full px-4 py-4 text-sm font-semibold leading-none text-white transition-colors duration-150 bg-gray-800 focus:outline-none rounded-bl-md hover:bg-gray-900"
                                @click="$vfm.show('video-modal', {id: localVideo.id})"
                        >Redigera</button>
                        <button class="flex items-center justify-center w-full px-4 py-4 text-sm font-semibold leading-none text-white transition-colors duration-150 bg-gray-800 focus:outline-none rounded-br-md hover:bg-gray-900"
                                @click="clearVideo"
                        >
                            Ta bort
                        </button>
                    </div>
                </div>
                <div class="absolute inset-0 transition-opacity duration-300 bg-black rounded-md opacity-0 group-hover:opacity-50 overlay " />
                <UiImagePresenter thumbnail
                                  :image="localVideo"
                                  class="block object-cover w-full h-full rounded-md"
                />
            </div>
        </div>
        <FMediaPicker :open="pickerOpen"
                      media-type="video"
                      @close="pickerOpen = false"
                      @item-picked="pickVideo"
        />
    </span>
</template>
<script>
import Video from '~/models/Video'
export default {
    name: 'FVideoInput',
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
            localVideo: null,
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
        hasVideo () {
            return this.localVideo.id
        }
    },
    created () {
        this.localVideo = { ...this.value }
        this.$emit('input', this.localVideo)
    },
    mounted () {
        // add the needed event listeners to the container
        this.$refs[this.randomRef].addEventListener('dragover', this.handleDragOver)
        this.$refs[this.randomRef].addEventListener('dragleave', this.handleDragLeave)

        // Listen on other group events
        if (this.group) {
            this.$eventBus.$on('video-selected', this.setVideo)
        }
    },
    beforeDestroy () {
        this.$refs[this.randomRef].removeEventListener('dragover', this.handleDragOver)
        this.$refs[this.randomRef].removeEventListener('dragleave', this.handleDragLeave)
    },
    methods: {
        handleDragOver () {
            this.isDraggingOver = true // add class on drag over
        },
        handleDragLeave () {
            this.isDraggingOver = false // remove class on drag leave
        },
        clearVideo () {
            this.$emit('input', null)
            this.localVideo = {}
            this.isDraggingOver = false
            this.emitVideoSelected()
        },
        pickVideo (id) {
            if (!this.multiple) {
                this.pickSingleVideo(id)
            }
            if (this.multiple) {
                this.pickMultipleVideos(id)
            }
            this.isDraggingOver = false
        },
        async pickMultipleVideos (id) {
            try {
                const payload = {
                    model_id: this.modelId
                }
                const { data } = await Video.attachToModel(id, this.modelName, payload)
                this.$emit('item-picked', data)
                this.pickerOpen = false
            } catch (error) {
                console.error(error)
            }
        },
        async pickSingleVideo (id) {
            try {
                const payload = {
                    key: this.fieldKey,
                    model_id: this.modelId
                }
                const { data } = await Video.show(id, payload)
                this.localVideo = data
                this.$emit('input', data)
                this.emitVideoSelected()
                this.pickerOpen = false
            } catch (error) {
                console.error(error)
            }
        },
        async handleNewVideo (item) {
            await this.pickVideo(item.id)
        },
        setVideo (payload) {
            if (this.group === payload.group && this.randomRef !== payload.randomRef) {
                this.localVideo = payload.data
                this.$emit('input', payload.data)
            }
        },
        emitVideoSelected () {
            // If a group is specified we want to sync the input with
            // all other inputs in the same group. Useful when having multiple
            // inputs for a bunch of langugages and the input is not translated
            if (this.group) {
                this.$eventBus.$emit('video-selected', {
                    group: this.group,
                    data: { ...this.localVideo },
                    randomRef: this.randomRef
                })
            }
        }
    }
}
</script>
