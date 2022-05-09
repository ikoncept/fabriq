<template>
    <div
        v-show="pagination.total_pages > 1"
        class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6"
    >
        <div class="flex justify-between flex-1 sm:hidden">
            <a
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:text-gray-500"
                href="#"
            >Previous</a>
            <a
                class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:text-gray-500"
                href="#"
            >Next</a>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Visar
                    <span
                        v-if="currentPageIsFirst"
                        class="font-medium"
                    >1</span>
                    <span
                        v-else
                        class="font-medium"
                    >{{ ((pagination.current_page - 1) * pagination.per_page) + 1 }}</span>
                    till
                    <span
                        v-if="currentPageIsFirst"
                        class="font-medium"
                        v-text="pagination.per_page > pagination.total ? pagination.total : pagination.per_page"
                    />
                    <span
                        v-else-if="! currentPageIsLast"
                        class="font-medium"
                    >{{ (pagination.current_page) * pagination.per_page }}</span>
                    av
                    <span class="font-medium">{{ pagination.total }}</span>
                </p>
            </div>
            <div>
                <nav
                    aria-label="Pagination"
                    class="relative z-0 inline-flex -space-x-px shadow-sm"
                >
                    <button
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 focus:outline-none rounded-l-md hover:bg-gray-50"
                        @click="stepBackwards"
                    >
                        <span class="sr-only">Previous</span>
                        <!-- Heroicon name: chevron-left -->
                        <svg
                            aria-hidden="true"
                            class="w-5 h-5"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                clip-rule="evenodd"
                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                fill-rule="evenodd"
                            />
                        </svg>
                    </button>
                    <button
                        v-for="(number, index) in numberBoxes"
                        :key="'nm'+index"
                        :class="pagination.current_page === number ? 'font-bold' : 'font-medium'"
                        :disabled=" ! Number.isInteger(number)"
                        class="relative inline-flex items-center px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 focus:outline-none hover:bg-gray-50"
                        @click="changePage(number)"
                    >
                        {{ number }}
                    </button>
                    <button
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 focus:outline-none rounded-r-md hover:bg-gray-50"
                        @click="stepForward"
                    >
                        <span class="sr-only">Next</span>
                        <!-- Heroicon name: chevron-right -->
                        <svg
                            aria-hidden="true"
                            class="w-5 h-5"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                clip-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                fill-rule="evenodd"
                            />
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'FPaginator',
    props: {
        pagination: {
            type: Object,
            required: true,
            default () {
                return {
                    total_pages: 1
                }
            }
        }
    },
    data () {
        return {
            config: {
                delta: 1
            }
        }
    },
    computed: {
        numberBoxes () {
            const range = Array(this.pagination.total_pages)
                .fill()
                .map((_, index) => index + 1)

            return range.reduce((pages, page) => {
                // allow adding of first and last pages
                if (page === 1 || page === this.pagination.total_pages) {
                    return [...pages, page]
                }

                // if within delta range add page
                if (page - this.config.delta <= this.pagination.current_page && page + this.config.delta >= this.pagination.current_page) {
                    return [...pages, page]
                }

                // otherwise add 'gap if gap was not the last item added.
                if (pages[pages.length - 1] !== '...') {
                    return [...pages, '...']
                }

                return pages
            }, [])
        },
        currentPageIsFirst () {
            if (this.pagination.current_page === 1) {
                return true
            }
            return false
        },
        currentPageIsLast () {
            if (this.pagination.total_pages === this.pagination.current_page) {
                return true
            }
            return false
        }
    },
    methods: {
        stepForward () {
            if (this.currentPageIsLast) {
                return
            }

            this.changePage(this.pagination.current_page + 1)
        },
        stepBackwards () {
            if (this.currentPageIsFirst) {
                return
            }

            this.changePage(this.pagination.current_page - 1)
        },
        changePage (pageNumber) {
            this.$emit('change-page', pageNumber)
        },
        getRange (start, end) {
            return Array(end - start + 1).fill().map((v, index) => index + start)
        }
    }
}
</script>
