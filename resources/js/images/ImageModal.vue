<template>
    <FModal v-model="show"
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
                <button type="button"
                        class="relative z-20 mr-6"
                >
                    <FConfirmDropdown confirm-question="Vill du ta bort bilden?"
                                      @confirmed="deleteImage"
                    >
                        <TrashIcon thin
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
                <form class="flex flex-col col-span-4 gap-y-4"
                      @submit.prevent="updateImage"
                >
                    <FInput ref="nameInput"
                            v-model="image.name"
                            rules="required"
                            name="name"
                            label="Bildnamn"
                            :suffix="'.' + image.extension"
                    />
                    <FInput v-model="image.alt_text"
                            label="Alt-text"
                            name="alt_text"
                            help-text="Alt-text visas b.la. för skärmläsare"
                    />
                    <FInput v-model="image.caption"
                            name="caption"
                            label="Bildtext"
                    />
                    <FSelect v-model="tags"
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
                        <a
                            :href="image.src"
                            download
                            without-loader

                            class="inline-block px-8 py-2 leading-none fabriq-btn btn-outline-royal"
                        >
                            Ladda ner
                        </a>
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
                    <UiImagePresenter v-if="image.id"
                                      :image="image"
                                      class="w-full mb-2 border border-gray-100 bg-checkered-lg"
                    />
                </div>
            </div>
        </div>
    </FModal>
</template>
<script>
import Image from '~/models/Image'
import Tag from '~/models/Tag'
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
            imageTags: []
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
        }
    }
}
</script>
