<template>
    <FModal
        v-model="show"
        name="file-modal"
        width="max-w-5xl"
        overflow="overflow-auto"
        @before-open="initModal"
        @closed="resetFile"
    >
        <template #title>
            <div class="flex items-center justify-between flex-1">
                <span
                    class="text-xl text-gray-700"
                    v-text="'Redigera fil'"
                />
                <button
                    type="button"
                    class="relative z-20 mr-6"
                >
                    <FConfirmDropdown
                        confirm-question="Vill du ta bort bilden?"
                        @confirmed="deleteFile"
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
                        :click="updateFile"
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
                    @submit.prevent="updateFile"
                >
                    <FInput
                        v-model="file.name"
                        rules="required"
                        name="name"
                        label="Filnamn"
                        :suffix="'.' + file.extension"
                    />
                    <!-- <f-input v-model="file.readable_name"
                             label="Alt-text"
                             help-text="Alt-text visas b.la. för skärmläsare"
                    /> -->
                    <!-- <f-input v-model="file.caption"
                             label="Bildtext"
                    /> -->
                    <FSelect
                        v-model="tags"
                        multiple
                        taggable
                        label="Taggar"
                        name="fileTags"
                        :reduce-fn="tag => tag"
                        class=""
                        :create-option="tag => ({ name: tag, value: null, type: 'files'})"
                        value-key="name"
                        option-label="name"
                        :push-tags="false"
                        :options="fileTags"
                    />
                    <div>
                        <FButton
                            type="button"
                            class="inline-block px-8 py-2 leading-none fabriq-btn btn-outline-royal"
                            spinner-color="text-royal-500"
                            :click="downloadFile"
                        >
                            Ladda ner
                        </FButton>
                    </div>
                    <button class="hidden" />
                </form>
                <div class="col-span-8">
                    <div class="flex justify-end mb-1 space-x-2">
                        <UiBadge>
                            {{ file.size | filesize }}
                        </UiBadge>
                        <UiBadge v-show="file.width">
                            {{ file.width }}×{{ file.height }}px
                        </UiBadge>
                    </div>
                    <UiImagePresenter
                        v-if="file.thumb_src"
                        :image="file"
                        thumbnail
                        class="w-full mb-2 border border-gray-100"
                    />
                </div>
            </div>
        </div>
    </FModal>
</template>
<script>
import Download from '~/models/Download'
import File from '~/models/File'
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
            file: {
                id: 0,
                size: 0
            },
            tags: [],
            fileTags: []
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
        async fetchFile (parameters) {
            const id = parameters.ref.params.id
            try {
                const payload = {
                    params: {
                        include: 'tags'
                    }
                }
                const { data } = await File.show(id, payload)
                this.file = data
                this.tags = [...data.tags.data]
            } catch (error) {
                console.error(error)
            }
        },
        async initModal (parameters) {
            try {
                const promises = [
                    this.fetchFile(parameters),
                    this.fetchFileTags()
                ]
                await Promise.all(promises)
            } catch (error) {
                console.error(error)
            }
        },
        async fetchFileTags () {
            try {
                const payload = {
                    params: {
                        'filter[type]': 'files'
                    }
                }
                const { data } = await Tag.index(payload)
                this.fileTags = data
            } catch (error) {
                console.error(error)
            }
        },
        resetFile () {
            this.file = {
                id: 0,
                size: 0
            }
        },
        async updateFile () {
            try {
                const object = { ...this.file }
                object.tags = [...this.nameTags]
                await File.update(this.file.id, object)
                this.$eventBus.$emit('file-updated')
                this.$toast.success({ title: 'Filen har uppdaterats!' })
                this.show = false
            } catch (error) {
                console.error(error)
            }
        },
        async deleteFile () {
            try {
                await File.destroy(this.file.id)
                this.$eventBus.$emit('file-updated')
                this.$toast.success({ title: 'Filen har raderats' })
                this.show = false
            } catch (error) {
                this.$toast.error({ title: 'Kunde inte radera filen' })
                console.error(error)
            }
        },
        async downloadFile () {
            const payload = {
                params: {
                    type: 'files'
                }
            }
            const { data, headers } = await Download.show(this.file.id, payload)
            await Download.handleBlobDownload(data, headers)
        }
    }
}
</script>
