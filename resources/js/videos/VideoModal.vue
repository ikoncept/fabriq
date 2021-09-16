<template>
    <FModal v-model="show"
            name="video-modal"
            width="max-w-5xl"
            overflow="overflow-auto"
            @before-open="initModal"
            @closed="resetVideo"
    >
        <template #title>
            <div class="flex items-center justify-between flex-1">
                <span
                    class="text-xl text-gray-700"
                    v-text="'Redigera video'"
                />
                <button type="button"
                        class="relative z-20 mr-6"
                >
                    <FConfirmDropdown confirm-question="Vill du ta bort bilden?"
                                      @confirmed="deleteVideo"
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
                        :click="updateVideo"
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
                      @submit.prevent="updateVideo"
                >
                    <FInput v-model="video.name"
                            rules="required"
                            name="name"
                            label="Filnamn"
                            :suffix="'.' + video.extension"
                    />
                    <FInput v-model="video.alt_text"
                            label="Alt-text"
                            name="alt_text"
                            help-text="Alt-text visas b.la. för skärmläsare"
                    />
                    <FInput v-model="video.caption"
                            label="Text"
                            name="caption"
                    />
                    <FSelect v-model="tags"
                             multiple
                             taggable
                             label="Taggar"
                             name="videoTags"
                             :reduce-fn="tag => tag"
                             class=""
                             :create-option="tag => ({ name: tag, value: null, type: 'videos'})"
                             value-key="name"
                             option-label="name"
                             :push-tags="false"
                             :options="videoTags"
                    />
                    <div>
                        <a
                            :href="video.src"
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
                            {{ video.size | filesize }}
                        </UiBadge>
                        <UiBadge v-show="video.width">
                            {{ video.width }}×{{ video.height }}px
                        </UiBadge>
                    </div>
                    <video controls
                           :poster="video.poster_src"
                           class="w-full"
                           :src="video.src"
                    >
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>
    </FModal>
</template>
<script>
import Video from '~/models/Video'
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
            video: {
                id: 0,
                size: 0
            },
            tags: [],
            videoTags: []
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
        async fetchVideo (parameters) {
            const id = parameters.ref.params.id
            try {
                const payload = {
                    params: {
                        include: 'tags'
                    }
                }
                const { data } = await Video.show(id, payload)
                this.video = data
                this.tags = [...data.tags.data]
            } catch (error) {
                console.error(error)
            }
        },
        async initModal (parameters) {
            try {
                const promises = [
                    this.fetchVideo(parameters),
                    this.fetchVideoTags()
                ]
                await Promise.all(promises)
            } catch (error) {
                console.error(error)
            }
        },
        async fetchVideoTags () {
            try {
                const payload = {
                    params: {
                        'filter[type]': 'videos'
                    }
                }
                const { data } = await Tag.index(payload)
                this.videoTags = data
            } catch (error) {
                console.error(error)
            }
        },
        resetVideo () {
            this.video = {
                id: 0,
                size: 0
            }
        },
        async updateVideo () {
            try {
                const object = { ...this.video }
                object.tags = [...this.nameTags]
                await Video.update(this.video.id, object)
                this.$eventBus.$emit('video-updated')
                this.$toast.success({ title: 'Videon har uppdaterats!' })
                this.show = false
            } catch (error) {
                console.error(error)
            }
        },
        async deleteVideo () {
            try {
                await Video.destroy(this.video.id)
                this.$eventBus.$emit('video-updated')
                this.$toast.success({ title: 'Videon har raderats' })
                this.show = false
            } catch (error) {
                this.$toast.error({ title: 'Kunde inte radera videon' })
                console.error(error)
            }
        }
    }
}
</script>
