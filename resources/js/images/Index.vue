<template>
    <div>
        <FModal
            v-model="showTagsModal"
            name="add-tags-modal"
            width="max-w-xl"
            :click-to-close="false"
            @before-open="fetchImageTags"
        >
            <template #title>
                Kategorisera bilder
            </template>
            <FSelect
                v-model="selectedTags"
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
            <template #actions>
                <div class="flex justify-end mt-4 space-x-4">
                    <div class="flex space-x-4">
                        <button
                            class="px-8 py-2.5 leading-none fabriq-btn btn-link"
                            @click="showTagsModal = false"
                        >
                            Stäng
                        </button>
                        <FButton
                            :click="addTagsToImages"
                            class="px-8 py-2.5 leading-none fabriq-btn btn-royal"
                        >
                            Spara
                        </FButton>
                    </div>
                </div>
            </template>
        </FModal>
        <UiSectionHeader class="mb-4">
            Bilder
            <template #tools>
                <div class="flex items-end space-x-2">
                    <div>
                        <AddImageFromUrlModal
                            name="addFromUrlModal"
                            @image-added="fetchImages"
                        />
                        <button
                            type="button"
                            class="inline-flex items-center px-2 py-1 text-xs fabriq-btn btn-gold"
                            @click="$vfm.show('addFromUrlModal')"
                        >
                            Lägg till från URL
                        </button>
                    </div>
                    <FUpload
                        v-if="uploadInit"
                        class=""
                        endpoint="/api/admin/uploads/images"
                        types="image/*"
                        upload-name="image"
                        @upload-queue-complete="fetchImages"
                    >
                        <template #button>
                            <button
                                type="button"
                                class="fabriq-btn  btn-royal py-2.5 px-4 inline-flex items-center"
                            >
                                Ladda upp bilder
                            </button>
                        </template>
                    </FUpload>
                </div>
            </template>
        </UiSectionHeader>
        <UiCard
            :padding="false"
            class="pb-6"
        >
            <template #header>
                <div class="flex justify-between px-6">
                    <div>
                        Uppladdade bilder
                    </div>
                    <div
                        class="flex items-center space-x-6 text-sm transition duration-300"
                        :class="hasCheckedRows ? 'text-gray-700' : 'text-gray-300'"
                    >
                        <Transition name="fade">
                            <span
                                v-if="hasCheckedRows"
                                class="mr-4 leading-none text-gray-600"
                            >Markerade bilder ({{ checkedRows.length }})</span>
                        </Transition>
                        <FButton
                            :click="downloadCheckedItems"
                            :disabled="!hasCheckedRows"
                            class="focus:outline-none"
                            spinner-color="text-royal-500"
                        >
                            <ArrowDownLineIcon
                                class="w-6 h-6 mr-3 "
                                thin
                            />
                            <span class="font-semibold">
                                Ladda ner
                            </span>
                        </FButton>
                        <FButton
                            class="focus:outline-none"
                            without-loader
                            :disabled="!hasCheckedRows"
                            spinner-color="text-royal-500"
                            :click="showModal"
                        >
                            <TagIcon
                                class="w-6 h-6 mr-3 "
                                thin
                            />
                            <span class="font-semibold">
                                Lägg till kategori
                            </span>
                        </FButton>
                        <FConfirmDropdown
                            confirm-question="Vill du ta bort de markerade bilderna?"
                            :disabled="!hasCheckedRows"
                            @confirmed="deleteCheckedImages()"
                        >
                            <div class="flex items-center space-x-3 font-semibold">
                                <TrashIcon
                                    class="w-6 h-6 "
                                    thin
                                />
                                <div>
                                    Ta bort
                                </div>
                            </div>
                        </FConfirmDropdown>
                    </div>
                </div>
            </template>
            <div class="max-w-full overflow-auto">
                <FTable
                    :columns="columns"
                    :rows="images"
                    paginated
                    :pagination="pagination"
                    :options="{shadow: false, defaultSort: 'created_at', sortDescending: true, clickableRows: true, checkableRows: true}"
                    @row-clicked="openImageModal"
                    @change-page="setPage"
                    @check-row="handleRowChecked"
                    @sort="setSort"
                >
                    <template #search>
                        <div class="px-6 border-b border-gray-100">
                            <div class="flex items-center ">
                                <SearchIcon class="w-6 h-6 mr-0 text-gray-300" />
                                <FSearchInput
                                    v-model="queryParams['filter[search]']"
                                    placeholder="Sök…"
                                    class="flex-1 px-6 py-4 text-sm text-gray-600 appearance-none focus:outline-none"
                                    @perform-search="performSearch"
                                    @clear-search="resetSearch"
                                />
                            </div>
                        </div>
                    </template>
                    <template #default="{ row: item, prop }">
                        <span
                            v-if="prop == 'image'"
                            class="block w-20"
                            @click="$vfm.show('image-modal', {id: item.id})"
                        >
                            <UiImagePresenter
                                :image="item"
                                thumbnail
                                class="w-20 cursor-pointer max-h-16"
                            />
                        </span>
                        <span v-else-if="prop == 'created_at'">
                            {{ item.created_at | localTime }}
                        </span>
                        <span v-else-if="prop == 'c_name'">
                            <div class="truncate text-ellipsis max-w-64">
                                {{ item.c_name }}
                            </div>
                        </span>
                        <span
                            v-else-if="prop == 'tags'"
                            class="flex space-x-2"
                        >
                            <UiBadge
                                v-for="(tag, index) in item.tags.data"
                                :key="index"
                            >{{ tag.name }}</UiBadge>
                        </span>
                        <span v-else-if="prop == 'size'">
                            {{ item.size | filesize }}
                        </span>
                        <span v-else-if="prop == 'dimensions'">

                            <UiBadge v-show="item.width">
                                {{ item.width }}×{{ item.height }}px
                            </UiBadge>
                        </span>
                        <span v-else-if="prop == 'edit'">
                            <button
                                class="px-4 py-2 btn-ghost fabriq-btn"
                                @click="openImageModal(item)"
                            ><PenToSquareIcon
                                class="w-6 h-6"
                                thin
                            /></button>
                            <FConfirmDropdown
                                confirm-question="Vill du ta bort denna bilden?"
                                @confirmed="deleteImage(item.id)"
                            >
                                <TrashIcon
                                    class="w-6 h-6 mt-1 text-gray-800 hover:text-red-500"
                                    thin
                                />
                            </FConfirmDropdown>
                        </span>
                    </template>
                </FTable>
            </div>
        </UiCard>
    </div>
</template>
<script>
import Tag from '~/models/Tag'
import Image from '~/models/Image'
import Download from '~/models/Download'
import AddImageFromUrlModal from '~/images/AddImageFromUrlModal'
export default {
    name: 'ImagesIndex',
    components: { AddImageFromUrlModal },
    beforeRouteLeave (from, to, next) {
        this.$eventBus.$off('image-updated', this.fetchImages)
        this.uploadInit = false
        next()
    },
    data () {
        return {
            images: [],
            search: '',
            uploadInit: true,
            checkedRows: [],
            suppressToast: false,
            showTagsModal: false,
            showUrlModal: false,
            imageUrl: '',
            columns: [
                {
                    key: 'image',
                    title: ''
                    // tdClasses: 'w-36',
                    // thClasses: 'w-36'
                },
                {
                    key: 'c_name',
                    title: 'Namn',
                    tdClasses: 'text-gray-600 font-medium w-24',

                    sortable: true
                },
                {
                    key: 'created_at',
                    title: 'Uppladdad',
                    sortable: true
                },
                {
                    key: 'alt_text',
                    title: 'Alt-text',
                    sortable: true
                },
                {
                    key: 'tags',
                    title: 'Taggar'
                },
                {
                    key: 'size',
                    title: 'Filstorlek',
                    sortable: true
                },
                {
                    key: 'dimensions',
                    title: 'Storlek',
                    sortable: false
                },
                {
                    key: 'edit',
                    title: '',
                    tdClasses: 'text-right',
                    thClasses: 'text-right'
                }
            ],
            pagination: {},
            queryParams: {
                number: 15,
                page: 1,
                sort: '-created_at',
                include: 'tags',
                'filter[search]': ''
            },
            imageTags: [],
            selectedTags: []
        }
    },
    computed: {
        hasCheckedRows () {
            return this.checkedRows.length > 0
        },
        nameTags () {
            return this.selectedTags.map(item => {
                return item.name
            })
        }
    },
    activated () {
        this.fetchImages()
        this.uploadInit = true
        this.$eventBus.$on('image-updated', this.fetchImages)
    },
    methods: {
        resetSearch () {
            this.search = ''
            this.queryParams['filter[search]'] = ''
            this.queryParams.page = 1
            this.fetchImages()
        },
        async fetchImages () {
            try {
                const payload = {
                    params: this.queryParams
                }
                const { data, meta } = await Image.index(payload)
                this.images = data
                this.pagination = meta.pagination
            } catch (error) {
                console.error(error)
            }
        },
        async deleteImage (id) {
            try {
                await Image.destroy(id)
                if (!this.suppressToast) {
                    this.$toast.success({ title: 'Bilden har tagits bort' })
                    this.fetchImages()
                }
            } catch (error) {
                console.error(error)
            }
        },
        setSort (sort) {
            this.queryParams.sort = sort
            this.fetchImages()
        },
        performSearch () {
            this.queryParams.page = 1
            this.fetchImages()
        },
        setPage (pageNumber) {
            this.queryParams.page = pageNumber
            this.fetchImages()
        },
        openImageModal (item) {
            this.$vfm.show('image-modal', { id: item.id })
        },
        handleRowChecked (rows) {
            this.checkedRows = rows
        },
        async deleteCheckedImages () {
            this.suppressToast = true
            const promises = []
            this.checkedRows.forEach(row => {
                promises.push({ deleteFunction: this.deleteImage, id: row })
            })
            await Promise.all(
                promises.map(async promise => {
                    await promise.deleteFunction(promise.id)
                })
            )
            this.suppressToast = false
            this.$eventBus.$emit('clear-checked-rows')
            this.$toast.success({ title: 'Bilderna har tagits bort' })
            this.fetchImages()
        },
        async downloadCheckedItems () {
            try {
                const payload = {
                    params: {
                        items: this.checkedRows,
                        type: 'images'
                    }
                }
                const { data, headers } = await Download.index(payload)
                Download.handleBlobDownload(data, headers)
            } catch (error) {
                console.error(error)
            }
        },
        showModal () {
            this.showTagsModal = true
        },
        async addTagsToImages () {
            try {
                const payload = {
                    models: this.checkedRows,
                    tags: this.nameTags,
                    model_type: 'images'
                }
                await Tag.store(payload)
                this.$toast.success({ title: 'Taggarna har lagts till!' })
                this.fetchImages()
                this.showTagsModal = false
            } catch (error) {
                console.error(error)
            }
        },
        async fetchImageTags () {
            this.selectedTags = []
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
        }

    }
}
</script>
