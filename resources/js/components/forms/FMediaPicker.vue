<template>
    <!-- This example requires Tailwind CSS v2.0+ -->
    <Transition :duration="201">
        <div v-show="open"
             key="mm"
             class="fixed inset-0 z-[60] overflow-hidden"
        >
            <div class="absolute inset-0 overflow-hidden">
                <section
                    aria-labelledby="slide-over-heading"
                    class="absolute inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16"
                >
                    <!--
        Slide-over panel, show/hide based on slide-over state.
                    -->
                    <Transition
                        enter-active-class="transition duration-200 ease-in-out transform sm:duration-500"
                        enter-class="translate-x-full"
                        enter-to-class="translate-x-0"
                        leave-active-class="transition duration-100 ease-in-out transform sm:duration-200"
                        leave-class="translate-x-0"
                        leave-to-class="translate-x-full"
                    >
                        <div v-if="open"
                             class="w-screen max-w-7xl"
                        >
                            <div
                                class="flex flex-col h-full py-6 overflow-y-scroll bg-white shadow-xl"
                            >
                                <div class="px-4 sm:px-6">
                                    <div class="flex items-start justify-between">
                                        <h2
                                            id="slide-over-heading"
                                            class="flex-1 text-lg font-medium text-gray-900"
                                            v-text="mediaType == 'image' ? 'Välj bild' : 'Välj fil'"
                                        />
                                        <div class="flex items-center ml-3 h-7">
                                            <FUpload
                                                v-if="mediaType === 'image'"
                                                class="mr-10"
                                                endpoint="/api/admin/uploads/images"
                                                types="image/*"
                                                upload-name="image"
                                                @upload-complete="fetchImages"
                                            />
                                            <FUpload
                                                v-else-if="mediaType === 'video'"
                                                class="mr-10"
                                                endpoint="/api/admin/uploads/videos"
                                                types="video/mp4,video/mov,video/quicktime,video/webm"
                                                upload-name="video"
                                                @upload-complete="fetchVideos"
                                            />
                                            <FUpload v-else
                                                     class="mr-10"
                                                     endpoint="/api/admin/uploads/files"
                                                     upload-name="file"
                                                     @upload-complete="fetchFiles"
                                            />
                                            <button
                                                class="text-gray-400 bg-white rounded-md hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                @click="close()"
                                            >
                                                <span class="sr-only">Close panel</span>
                                                <!-- Heroicon name: x -->
                                                <svg
                                                    aria-hidden="true"
                                                    class="w-6 h-6"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                >
                                                    <path
                                                        d="M6 18L18 6M6 6l12 12"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                    />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="relative z-30 flex-1 px-6 mt-6 sm:px-6=">
                                    <FTable :columns="columns"
                                            :rows="items"
                                            paginated
                                            :options="{shadow: false, defaultSort: 'created_at', sortDescending: true, clickableRows: true}"
                                            :pagination="pagination"
                                            @change-page="setPage"
                                            @row-clicked="handleRowClicked"
                                            @sort="setSort"
                                    >
                                        <!-- <template #search>
                                            <div class="px-6 border-b border-gray-100">
                                                <div class="flex items-center ">
                                                    <search-icon class="w-6 h-6 mr-0 text-gray-300" />
                                                    <input v-model="search"
                                                           type="search"
                                                           class="flex-1 px-6 py-4 text-xl text-gray-600 appearance-none focus:outline-none"
                                                    >
                                                </div>
                                            </div>
                                        </template> -->
                                        <template #search>
                                            <div class="px-6 border-b border-gray-100">
                                                <div class="flex items-center ">
                                                    <SearchIcon class="w-6 h-6 mr-0 text-gray-300" />
                                                    <FSearchInput v-model="queryParams['filter[search]']"
                                                                  placeholder="Sök…"
                                                                  class="flex-1 px-6 py-4 text-sm text-gray-600 appearance-none focus:outline-none"
                                                                  @perform-search="fetchItems"
                                                                  @clear-search="resetSearch"
                                                    />
                                                </div>
                                            </div>
                                        </template>
                                        <template #default="{ row: item, prop }">
                                            <span v-if="prop == 'image'">
                                                <UiImagePresenter :image="item"
                                                                  thumbnail
                                                                  class="w-20 max-h-16"
                                                />
                                            </span>
                                            <span v-else-if="prop == 'created_at'">
                                                {{ item.created_at | localTime }}
                                            </span>
                                            <span v-else-if="prop == 'alt_text'"
                                                  class="pb-1 font-medium border-b-2 border-gray-500 border-dotted text-royal-500"
                                            >
                                                Lägg till alt-text
                                            </span>
                                            <span v-else-if="prop == 'size'">
                                                {{ item.size | filesize }}
                                            </span>
                                            <span v-else-if="prop == 'tags'"
                                                  class="flex space-x-2"
                                            >
                                                <UiBadge v-for="(tag, index) in item.tags.data"
                                                         :key="index"
                                                >{{ tag.name }}</UiBadge>
                                            </span>
                                            <span v-else-if="prop == 'edit'">
                                                <!-- <button class="px-4 py-2 btn-ghost fabriq-btn">Redigera</button> -->
                                                <button
                                                    class="px-4 py-2 text-sm font-semibold leading-none fabriq-btn btn-ghost"
                                                    @click="pickItem(item.id)"
                                                >
                                                    Välj
                                                </button>
                                            </span>
                                        </template>
                                    </FTable>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </section>
            </div>
        </div>
    </Transition>
</template>
<script>
import Image from '~/models/Image'
import File from '~/models/File'
import Video from '~/models/Video'
export default {
    name: 'FMediaPicker',
    props: {
        mediaType: {
            type: String,
            default: 'image'
        },
        open: {
            type: Boolean,
            default: false
        }
    },
    data () {
        return {
            items: [],
            search: '',
            columns: [
                {
                    key: 'image',
                    title: '',
                    tdClasses: 'w-36',
                    thClasses: 'w-36'
                },
                {
                    key: 'c_name',
                    title: 'Namn',
                    tdClasses: 'text-gray-600 font-medium',
                    sortable: true
                },
                {
                    key: 'created_at',
                    title: 'Uppladdad',
                    sortable: true
                },
                // {
                //     key: 'alt_text',
                //     title: 'Alt-text'
                // },
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
                number: 10,
                page: 1,
                include: 'tags',
                sort: '-created_at',
                'filter[search]': ''
            }
        }
    },
    watch: {
        open (value) {
            if (value) {
                this.fetchItems()
                window.addEventListener('keyup', this.onEscapeKeyUp)
            }
        }
    },
    methods: {
        close () {
            this.$emit('close')
            this.search = ''
            window.removeEventListener('keyup', this.onEscapeKeyUp)
        },
        onEscapeKeyUp (event) {
            if (event.which === 27 && this.open) {
                this.close()
            }
        },
        pickItem (id) {
            this.$emit('item-picked', id)
        },
        async fetchImages () {
            try {
                const payload = {
                    params: this.queryParams
                }
                const { data, meta } = await Image.index(payload)
                this.items = data
                this.pagination = meta.pagination
            } catch (error) {
                console.error(error)
            }
        },
        async fetchVideos () {
            try {
                const payload = {
                    params: this.queryParams
                }
                const { data, meta } = await Video.index(payload)
                this.items = data
                this.pagination = meta.pagination
            } catch (error) {
                console.error(error)
            }
        },
        async fetchFiles () {
            try {
                const payload = {
                    params: this.queryParams
                }
                const { data, meta } = await File.index(payload)
                this.items = data
                this.pagination = meta.pagination
            } catch (error) {
                console.error(error)
            }
        },
        fetchItems () {
            if (this.mediaType === 'image') {
                this.fetchImages()
            } else if (this.mediaType === 'video') {
                this.fetchVideos()
            } else {
                this.fetchFiles()
            }
        },
        handleRowClicked (item) {
            this.pickItem(item.id)
        },
        setSort (sort) {
            this.queryParams.sort = sort
            this.fetchItems()
        },
        setPage (pageNumber) {
            this.queryParams.page = pageNumber
            this.fetchItems()
        },
        resetSearch () {
            this.search = ''
            this.queryParams['filter[search]'] = ''
            this.queryParams.page = 1
            this.fetchItems()
        }
    }
}
</script>
