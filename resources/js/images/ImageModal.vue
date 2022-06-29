<template>
    <FModal
        v-model="show"
        name="image-modal"
        width="max-w-5xl"
        overflow="overflow-auto"
        @before-open="initModal"
        @closed="resetImage"
    >
        <template #title>
            <div class="flex items-center justify-between flex-1">
                <span
                    class="text-xl text-gray-700"
                    v-text="'Redigera bild'"
                />
                <button
                    type="button"
                    class="relative z-20 mr-6"
                >
                    <FConfirmDropdown
                        confirm-question="Vill du ta bort bilden?"
                        @confirmed="deleteImage"
                    >
                        <TrashIcon
                            thin
                            class="w-6 h-6 mt-1 text-gray-800 transition-colors duration-150 hover:text-red-500"
                        />
                    </FConfirmDropdown>
                </button>
            </div>
        </template>
        <template #actions>
            <div class="flex justify-end space-x-4">
                <div class="flex space-x-4">
                    <button
                        class="px-8 py-2.5 leading-none fabriq-btn btn-link"
                        @click="show = false"
                    >
                        Stäng
                    </button>
                    <FButton
                        :click="updateImage"
                        class="px-8 py-2.5 leading-none fabriq-btn btn-royal"
                    >
                        Spara
                    </FButton>
                </div>
            </div>
        </template>
        <div class="relative z-40 py-2">
            <div class="grid grid-cols-12 gap-x-6">
                <form
                    class="flex flex-col col-span-4 gap-y-4"
                    @submit.prevent="updateImage"
                >
                    <FInput
                        ref="nameInput"
                        v-model="image.name"
                        rules="required"
                        name="name"
                        label="Bildnamn"
                        :suffix="'.' + image.extension"
                    />
                    <FInput
                        v-model="image.alt_text"
                        label="Alt-text"
                        name="alt_text"
                        help-text="Alt-text visas b.la. för skärmläsare"
                    />
                    <FInput
                        v-model="image.caption"
                        name="caption"
                        label="Bildtext"
                    />
                    <FSelect
                        v-model="tags"
                        multiple
                        taggable
                        label="Taggar"
                        name="tags"
                        :reduce-fn="tag => tag"
                        class=""
                        :create-option="tag => ({ name: tag, value: null, type: 'images'})"
                        value-key="name"
                        option-label="name"
                        :push-tags="false"
                        :options="imageTags"
                    />
                    <div>
                        <FSwitch
                            v-model="image.custom_crop"
                            name="custom_crop"
                        >
                            Anpassa beskärning
                        </FSwitch>
                    </div>
                    <button class="hidden" />
                </form>
                <div class="col-span-8">
                    <div class="flex justify-end mb-1 space-x-2">
                        <UiBadge>
                            {{ image.size | filesize }}
                        </UiBadge>
                        <UiBadge v-show="image.width">
                            {{ image.width }}×{{ image.height }}px
                        </UiBadge>
                    </div>
                    <div
                        ref="image"
                        class="relative cursor-pointer duration"
                        @click="getCoords"
                    >
                        <Transition
                            enter-active-class="transition ease-out transform duration-600"
                            enter-class="scale-150 opacity-0"
                            enter-to-class="scale-100 opacity-100"
                            leave-active-class="duration-100 ease-in transform"
                            leave-class="scale-100"
                            leave-to-class="scale-0 opacity-0 "
                        >
                            <div
                                v-if="image.custom_crop"
                                class="absolute z-10 w-4 h-4 transition-all duration-75 ease-out border-2 border-white rounded-full shadow bg-gold-500 dot"
                                :style="{left: image.x_position, top: image.y_position}"
                            />
                        </Transition>
                        <UiImagePresenter
                            v-if="image.id"
                            :image="image"
                            disable-custom-crop
                            class="w-full mb-2 border border-gray-100 pointer-events-none bg-checkered-lg"
                        />
                    </div>
                    <div class="flex space-x-6">
                        <div>
                            <FButton
                                type="button"
                                class="inline-block px-8 py-2 mb-4 leading-none fabriq-btn btn-outline-royal whitespace-nowrap"
                                spinner-color="text-royal-500"
                                :click="downloadFile"
                            >
                                Ladda ner
                            </FButton>
                        </div>
                        <div v-if="image.webp_src">
                            <FButton
                                type="button"
                                class="inline-block px-8 py-2 mb-4 leading-none fabriq-btn btn-outline-royal whitespace-nowrap"
                                spinner-color="text-royal-500"
                                :click="downloadWebP"
                            >
                                Ladda ner WebP
                            </FButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </FModal>
</template>
<script>
import Download from '@/models/Download.js'
import Image from '@/models/Image.js'
import Tag from '@/models/Tag.js'
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
            image: {
                id: 0,
                size: 0
            },
            tags: [],
            imageTags: [],
            y_position: '50%',
            x_position: '50%'
        }
    },
    computed: {
        nameTags () {
            return this.tags.map(item => {
                return item.name
            })
        }
    },
    methods: {
        async fetchImage (parameters) {
            const id = parameters.ref.params.id
            try {
                const payload = {
                    params: {
                        include: 'tags'
                    }
                }
                const { data } = await Image.show(id, payload)
                this.image = data
                this.tags = [...data.tags.data]
            } catch (error) {
                console.error(error)
            }
        },
        async initModal (parameters) {
            try {
                const promises = [
                    this.fetchImage(parameters),
                    this.fetchImageTags()
                ]
                await Promise.all(promises)
            } catch (error) {
                console.error(error)
            }
        },
        async fetchImageTags () {
            try {
                const payload = {
                    params: {
                        'filter[type]': 'images'
                    }
                }
                const { data } = await Tag.index(payload)
                this.imageTags = data
            } catch (error) {
                console.error(error)
            }
        },
        resetImage () {
            this.image = {
                id: 0,
                size: 0
            }
        },
        async updateImage () {
            try {
                const object = { ...this.image }
                object.tags = [...this.nameTags]
                console.log(object)
                // return
                await Image.update(this.image.id, object)
                this.$eventBus.$emit('image-updated')
                this.$toast.success({ title: 'Bilden har uppdaterats!' })
                this.show = false
            } catch (error) {
                console.error(error)
            }
        },
        async deleteImage () {
            try {
                await Image.destroy(this.image.id)
                this.$eventBus.$emit('image-updated')
                this.$toast.success({ title: 'Bilden har raderats' })
                this.show = false
            } catch (error) {
                this.$toast.error({ title: 'Kunde inte radera bilden' })
                console.error(error)
            }
        },
        async downloadFile () {
            const payload = {
                params: {
                    type: 'images'
                }
            }
            const { data, headers } = await Download.show(this.image.id, payload)
            await Download.handleBlobDownload(data, headers)
        },
        async downloadWebP () {
            const payload = {
                params: {
                    type: 'images',
                    webp: true
                }
            }
            const { data, headers } = await Download.show(this.image.id, payload)
            await Download.handleBlobDownload(data, headers)
        },
        getCoords (event) {
            if (!this.image.custom_crop) {
                return
            }
            const rect = event.currentTarget.getBoundingClientRect()
            const image = this.$refs.image
            const x = event.clientX - rect.left // x position within the element.
            const y = event.clientY - rect.top // y position within the element.
            const xPos = Math.round((x / image.offsetWidth) * 100)
            const yPos = Math.round((y / image.offsetHeight) * 100)
            this.image.y_position = yPos + '%'
            this.image.x_position = xPos + '%'
        }
    }
}
</script>
