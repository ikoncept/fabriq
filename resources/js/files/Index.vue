<template>
    <div>
        <FModal
            v-model="showTagsModal"
            name="add-tags-modal"
            width="max-w-xl"
            :click-to-close="false"
            @before-open="fetchFileTags"
        >
            <template #title>
                Kategorisera filer
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
                :options="fileTags"
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
                            :click="addTagsToFiles"
                            class="px-8 py-2.5 leading-none fabriq-btn btn-royal"
                        >
                            Spara
                        </FButton>
                    </div>
                </div>
            </template>
        </FModal>
        <UiSectionHeader class="mb-4">
            Filer
            <template #tools>
                <FUpload
                    v-if="uploadInit"
                    class="mr-10"
                    endpoint="/api/admin/uploads/files"
                    upload-name="file"
                    @upload-queue-complete="fetchFiles"
                >
                    <template #button>
                        <button
                            type="button"
                            class="fabriq-btn ml-10 -mr-5 btn-royal py-2.5 px-4 inline-flex items-center"
                        >
                            Ladda upp filer
                        </button>
                    </template>
                </FUpload>
            </template>
        </UiSectionHeader>
        <UiCard
            :padding="false"
            class="pb-6"
        >
            <template #header>
                <div class="flex justify-between px-6">
                    <div>
                        Uppladdade filer
                    </div>
                    <div
                        class="flex items-center space-x-6 text-sm transition duration-300"
                        :class="hasCheckedRows ? 'text-gray-700' : 'text-gray-300'"
                    >
                        <Transition name="fade">
                            <span
                                v-if="hasCheckedRows"
                                class="mr-4 leading-none text-gray-600"
                            >Markerade filer ({{ checkedRows.length }})</span>
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
                            @confirmed="deleteCheckedFiles()"
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
                    :rows="files"
                    paginated
                    :pagination="pagination"
                    :options="{shadow: false, defaultSort: 'created_at', sortDescending: true, clickableRows: true, checkableRows: true}"
                    @row-clicked="openFileModal"
                    @check-row="handleRowChecked"
                    @change-page="setPage"
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
                            v-if="prop == 'file'"
                            class="block"
                            @click="$vfm.show('file-modal', {id: item.id})"
                        >
                            <UiImagePresenter
                                v-if="item.thumb_src"
                                :image="item"
                                thumbnail
                                class="border cursor-pointer min-w-16"
                            />
                            <div v-else>
                                <span class=" items-center justify-center h-8 px-1 font-semibold font-mono text-xs rounded-full min-w-[2rem] bg-royal-500 text-gold-300 inline-flex">
                                    {{ item.extension }}
                                </span>
                            </div>
                        </span>
                        <span v-else-if="prop == 'created_at'">
                            {{ item.created_at | localTime }}
                        </span>
                        <span v-else-if="prop == 'file_name'">
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
                        <span v-else-if="prop == 'edit'">
                            <button
                                class="px-4 py-2 btn-ghost fabriq-btn"
                                @click="openFileModal(item)"
                            ><PenToSquareIcon
                                class="w-6 h-6"
                                thin
                            /></button>
                            <FConfirmDropdown
                                confirm-question="Vill du ta bort denna filen?"
                                @confirmed="deleteFile(item.id)"
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
import Download from '@/models/Download.js'
import File from '@/models/File.js'
import Tag from '@/models/Tag.js'
export default {
    name: 'FilesIndex',
    beforeRouteLeave (from, to, next) {
        this.$eventBus.$off('file-updated', this.fetchFiles)
        this.uploadInit = false
        next()
    },
    data () {
        return {
            files: [],
            search: '',
            uploadInit: true,
            showTagsModal: false,
            suppressToast: false,
            columns: [
                {
                    key: 'file',
                    title: '',
                    thClasses: 'w-5',
                    tdClasses: 'w-5'
                },
                {
                    key: 'file_name',
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
                    key: 'tags',
                    title: 'Taggar'
                },
                {
                    key: 'size',
                    title: 'Storlek',
                    sortable: true
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
            checkedRows: [],
            fileTags: [],
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
        this.fetchFiles()
        this.uploadInit = true
        this.$eventBus.$on('file-updated', this.fetchFiles)
    },

    methods: {
        resetSearch () {
            this.search = ''
            this.queryParams['filter[search]'] = ''
            this.queryParams.page = 1
            this.fetchFiles()
        },
        async fetchFiles () {
            try {
                const payload = {
                    params: this.queryParams
                }
                const { data, meta } = await File.index(payload)
                this.files = data
                this.pagination = meta.pagination
            } catch (error) {
                console.error(error)
            }
        },
        async deleteFile (id) {
            try {
                await File.destroy(id)
                if (!this.suppressToast) {
                    this.$toast.success({ title: 'Filen har tagits bort' })
                    this.fetchFiles()
                }
            } catch (error) {
                console.error(error)
            }
        },
        setSort (sort) {
            this.queryParams.sort = sort
            this.fetchFiles()
        },
        performSearch () {
            this.queryParams.page = 1
            this.fetchFiles()
        },
        setPage (pageNumber) {
            this.queryParams.page = pageNumber
            this.fetchFiles()
        },
        openFileModal (item) {
            this.$vfm.show('file-modal', { id: item.id })
        },
        async fetchFileTags () {
            this.selectedTags = []
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
        handleRowChecked (rows) {
            this.checkedRows = rows
        },
        showModal () {
            this.showTagsModal = true
        },
        async addTagsToFiles () {
            try {
                const payload = {
                    models: this.checkedRows,
                    tags: this.nameTags,
                    model_type: 'files'
                }
                await Tag.store(payload)
                this.$toast.success({ title: 'Taggarna har lagts till!' })
                this.fetchFiles()
                this.showTagsModal = false
            } catch (error) {
                console.error(error)
            }
        },
        async downloadCheckedItems () {
            try {
                const payload = {
                    params: {
                        items: this.checkedRows,
                        type: 'files'
                    }
                }
                const { data, headers } = await Download.index(payload)
                Download.handleBlobDownload(data, headers)
            } catch (error) {
                console.error(error)
            }
        },
        async deleteCheckedFiles () {
            this.suppressToast = true
            const promises = []
            this.checkedRows.forEach(row => {
                promises.push({ deleteFunction: this.deleteFile, id: row })
            })
            await Promise.all(
                promises.map(async promise => {
                    await promise.deleteFunction(promise.id)
                })
            )
            this.suppressToast = false
            this.$eventBus.$emit('clear-checked-rows')
            this.$toast.success({ title: 'Filerna har tagits bort' })
            this.fetchFiles()
        }
    }
}
</script>
