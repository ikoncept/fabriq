<template>
    <div class="overflow-x-auto md:overflow-x-visible">
        <div class="inline-block min-w-full">
            <div
                :class="{'shadow-md sm:rounded-lg': mergedOptions.shadow}"
                class="border-b border-gray-200 "
            >
                <slot name="search" />
                <table class="table min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th
                                v-if="mergedOptions.checkableRows"
                                class="w-12"
                            >
                                <!-- <input
                                    v-model="allRowsChecked"
                                    type="checkbox"
                                    class="w-5 h-5 mt-1 fabriq-checkbox form-checkbox focus:outline-none focus:ring-offset-3 focus:ring-1 focus:ring-royal-300"
                                    :indeterminate.prop="checkedRows.length > 0 && !allRowsChecked"
                                    @change="toggleAllRows"
                                > -->
                            </th>
                            <th
                                v-for="(column) in columns"
                                :key="column.key"
                                :class="[column.thClasses, {'cursor-pointer select-none': column.sortable, 'bg-gray-50': mergedOptions.shadow}, 'text-left px-6 py-3 text-sm font-medium text-gray-600 tracking-wide whitespace-nowrap']"
                                scope="col"
                                @click="thClicked(column)"
                            >
                                <span>{{ column.title }}</span>
                                <span
                                    v-if="column.sortable"
                                    class="inline-block ml-1 align-bottom"
                                >
                                    <span v-if="currentSortable.includes(column.key)">
                                        <ChevronUpIcon
                                            v-show="descending"
                                            class="w-2 leading-none text-gray-400"
                                        />
                                        <ChevronDownIcon
                                            v-show="!descending"
                                            class="w-2 text-gray-400"
                                        />
                                    </span>
                                    <span v-else>
                                        <ChevronSortIcon class="w-2" />
                                    </span>
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="(row, index) in rows"
                            :key="index"
                            :class="{'cursor-pointer': mergedOptions.clickableRows}"
                            class="transition-colors duration-200 bg-white group hover:bg-gray-50"
                            @click="rowClicked(row)"
                        >
                            <td
                                v-if="mergedOptions.checkableRows"
                                class="w-8 px-4 text-center "
                            >
                                <input
                                    v-model="checkedRows"
                                    type="checkbox"
                                    class="w-5 h-5 fabriq-checkbox form-checkbox focus:outline-none focus:ring-offset-3 focus:ring-1 focus:ring-royal-300"
                                    :value="row[mergedOptions.rowKey]"
                                    @change="$emit('check-row', checkedRows)"
                                    @click.stop
                                >
                            </td>
                            <td
                                v-for="prop in columns"
                                :key="prop.key"
                                :class="[prop.tdClasses, 'text-gray-500']"
                            >
                                <div class="px-6 py-4 text-sm whitespace-nowrap">
                                    <slot
                                        v-if="prop.dotNotated"
                                        :prop="prop.key"
                                        :row="row"
                                    >
                                        <!-- {{ row[prop.key] }} -->
                                        {{ prop.key.split('.').reduce((o,i)=>o[i], row) }}
                                    </slot>
                                    <slot
                                        v-else
                                        :prop="prop.key"
                                        :row="row"
                                    >
                                        {{ row[prop.key] }}
                                    </slot>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody
                        v-if="rows.length === 0"
                        class="bg-white divide-y divide-gray-200"
                    >
                        <tr>
                            <td
                                class="py-4 text-sm font-semibold text-center text-gray-500"
                                colspan="100%"
                                v-text="mergedOptions.emptyText"
                            />
                        </tr>
                    </tbody>
                    <tfoot v-if="hasTableFooterSlot">
                        <tr class="bg-white">
                            <td
                                colspan="100%"
                                class="px-6 py-4 text-right whitespace-nowrap"
                            >
                                <slot name="footer" />
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <FPaginator
                    v-if="paginated"
                    :pagination="pagination"
                    @change-page="emitChangePage"
                />
            </div>
        </div>
    </div>
</template>
<script>
import FPaginator from '~/components/table/FPaginator'
export default {
    name: 'FTable',
    components: { FPaginator },
    props: {
        columns: {
            type: Array,
            required: true
        },
        rows: {
            type: Array,
            required: true,
            default: () => []
        },
        options: {
            required: false,
            type: Object,
            default: () => ({})
        },
        paginated: {
            type: Boolean,
            required: false,
            default: false
        },
        pagination: {
            type: Object,
            required: false,
            default () {
                return {
                    count: 0,
                    current_page: 1,
                    links: {},
                    per_page: 0,
                    total: 0,
                    total_pages: 0
                }
            }
        }
    },
    data () {
        return {
            defaultOptions: {
                sortDescending: true,
                clickableRows: false,
                rowGroupHover: true,
                paginated: false,
                defaultSort: 'id',
                shadow: true,
                emptyText: 'Det finns ingen data att visa',
                checkableRows: false,
                rowKey: 'id',
                search: false
            },
            currentSortable: '',
            descending: true,
            checkedRows: [],
            parentCheck: false
        }
    },
    computed: {
        mergedOptions () {
            return {
                ...this.defaultOptions,
                ...this.options
            }
        },
        allRowsChecked: {
            get () {
                if (this.rows.length === 0) {
                    return false
                }
                if (!this.mergedOptions.checkableRows) {
                    return false
                }
                if (this.checkedRows.length === this.rows.length) {
                    return true
                }
                return false
            },
            set () {

            }
        },
        hasTableFooterSlot () {
            return !!this.$slots.footer
        }
    },
    mounted () {
        this.currentSortable = this.mergedOptions.defaultSort
        this.currentDirection = this.mergedOptions.sortDescending
        this.descending = this.mergedOptions.sortDescending
        this.$eventBus.$on('clear-checked-rows', this.clearCheckedRows)
    },
    beforeDestroy () {
        this.$eventBus.$off('clear-checked-rows', this.clearCheckedRows)
    },
    methods: {
        emitChangePage (pageNumber) {
            this.$emit('change-page', pageNumber)
        },
        toggleAllRows () {
            if (this.allRowsChecked) {
                this.clearCheckedRows()
            } else {
                this.checkedRows = [...this.rows].map(item => {
                    return item.id
                })
                this.$emit('check-row', this.checkedRows)
            }
        },
        clearCheckedRows () {
            this.checkedRows = []
            this.$emit('check-row', this.checkedRows)
        },
        thClicked (column) {
            const sortKey = column.sortKey ?? column.key

            if (!column.sortable) {
                return
            }

            if (this.currentSortable === sortKey) {
                console.warn('letsgo')
                this.descending = !this.descending
            }

            this.currentSortable = sortKey
            const emitData = this.descending ? '-' + sortKey : sortKey
            this.$emit('sort', emitData)
        },
        rowClicked (row) {
            if (this.options.clickableRows) {
                this.$emit('row-clicked', row)
            }
        },
        checkRow (row) {
            this.$emit('check-row', this.checkedRows)
        }
    }
}
</script>
