<template>
    <div class="flex justify-center w-full">
        <Transition name="fade">
            <div
                v-show="isUploading && ! withoutLoader"
                class="flex items-center text-sm"
            >
                <div>
                    Laddar upp {{ numUploads }} filer
                    <div
                        v-show="progress === 100"
                        class="absolute text-xs processing-text font-semibod"
                    >
                        Bearbetar filer
                    </div>
                </div>
                <span class="inline-block w-12 ml-1">({{ progressPercentage }}%)</span>
                <span class="inline-flex mx-4 animate-spin">
                    <SpinIcon class="w-5 h-5 text-royal-500" />
                </span>
            </div>
        </Transition>
        <Transition name="fade">
            {{ progress }}
        </Transition>
        <div
            v-if="! withoutButton"
            ref="uploadButton"
        >
            <slot name="button">
                <button
                    class="px-6 leading-none py-2.5 text-sm font-semibold fabriq-btn btn-royal"
                >
                    Ladda upp
                </button>
            </slot>
        </div>
    </div>
</template>

<script>
import Dropzone from 'dropzone'
import Cookies from 'js-cookie'
export default {
    name: 'FUpload',
    props: {
        value: {
            type: [String, Number, Object, Array],
            default: ''
        },
        dropArea: {
            type: Boolean,
            default: false
        },
        maxItems: {
            type: Number,
            default: 999
        },
        uploadName: {
            type: String,
            default: 'file'
        },
        endpoint: {
            type: String,
            required: true,
            default: ''
        },
        types: {
            type: String,
            default: ''
        },
        payload: {
            type: Object,
            required: false,
            default () {
                return {}
            }
        },
        withoutButton: {
            type: Boolean,
            default: false
        },
        withoutLoader: {
            type: Boolean,
            default: false
        },
        dropRef: {
            type: String,
            default: ''
        },
        buttonRef: {
            type: String,
            default: ''
        }
    },
    data () {
        return {
            UploadDropzone: null,
            numUploads: 0,
            numCompleted: 1,
            uploadComplete: false,
            isUploading: false,
            progress: 0
        }
    },
    computed: {
        progressPercentage () {
            if (this.progress === 100) {
                return 100
            }
            return (this.progress).toFixed(0)
        },
        dropElement () {
            if (this.dropRef) {
                return this.$parent.$refs[this.dropRef]
            }
            return document.body
        },
        buttonElement () {
            if (this.buttonRef) {
                return this.$parent.$refs[this.buttonRef]
            }
            return this.$refs.uploadButton
        },
        hasGlobalDrop () {
            if (this.dropRef) {
                return false
            }
            return true
        }
    },
    mounted () {
        this.initDropzone()
        if (this.hasGlobalDrop) {
            document.addEventListener('paste', this.handlePaste)
        }
    },
    beforeDestroy () {
        this.UploadDropzone.destroy()
        if (this.hasGlobalDrop) {
            console.log('detaching paste')
            document.removeEventListener('paste', this.handlePaste)
        }
    },
    methods: {
        initDropzone () {
            this.UploadDropzone = new Dropzone(this.dropElement, {
                paramName: this.uploadName,
                maxFilesize: 6000,
                maxFiles: this.maxItems,
                url: this.endpoint,
                createImageThumbnails: false,
                parallelUploads: 5,
                dictInvalidFileType: 'Filtypen är inte tillåten',
                acceptedFiles: this.types,
                autoQueue: true, // Make sure the modelFiles aren't queued until manually added
                clickable: this.buttonElement,
                previewsContainer: false,
                params: this.payload,
                withCredentials: true,
                headers: { 'X-XSRF-TOKEN': Cookies.get('XSRF-TOKEN') }
                // headers: {
                // 'X-CSRF-TOKEN': window.fabriqCms.csrfToken
                // }
            })
            this.UploadDropzone.on('addedfile', (file) => {
                this.numUploads++
                this.isUploading = true
                this.$emit('added-file')
            })
            this.UploadDropzone.on('totaluploadprogress', (progress) => {
                this.progress = progress
            })
            this.UploadDropzone.on('queuecomplete', (progress) => {
                setTimeout(() => {
                    this.numUploads = 0
                    this.numCompleted = 1
                    this.progress = 0
                }, 1600)
                setTimeout(() => {
                    this.isUploading = 0
                    this.$emit('upload-queue-complete')
                }, 600)
            })
            this.UploadDropzone.on('error', (file, errorMessage) => {
                this.$toast.warning({ title: 'Kunde inte ladda upp filen', message: errorMessage })
                this.$emit('error', errorMessage)
                this.UploadDropzone.removeAllFiles()
                console.error(file, errorMessage)
            })
            this.UploadDropzone.on('success', (file, response) => {
                this.errors = []
                this.$emit('upload-complete', response)
                this.$emit('input', response)
            })
            this.UploadDropzone.on('maxfilesexceeded', (file) => {
                this.UploadDropzone.removeAllFiles()
                this.UploadDropzone.addFile(file)
            })
        },
        handlePaste (event) {
            const items = (event.clipboardData || event.originalEvent.clipboardData).items
            for (const index in items) {
                const item = items[index]
                if (item.kind === 'file') {
                    this.UploadDropzone.addFile(item.getAsFile())
                }
            }
        }
    }
}

</script>
