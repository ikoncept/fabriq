<template>
    <div>
        <CreateModal
            name="createSmartBlockModal"
            @validated="createSmartBlock"
            @opened="$refs.nameInput.$refs.input.focus()"
        >
            <template #title>
                Lägg till smart block
            </template>
            <FInput
                ref="nameInput"
                v-model="newSmartBlock.name"
                label="Namn"
                rules="required"
                class="mb-6"
                name="name"
            />
        </CreateModal>
        <UiSectionHeader class="mb-4">
            Smarta block
            <template #tools>
                <button
                    type="button"
                    class="fabriq-btn ml-10  btn-royal py-2.5 px-4 inline-flex items-center"
                    @click="$vfm.show('createSmartBlockModal')"
                >
                    Lägg till smart block
                </button>
            </template>
        </UiSectionHeader>
        <UiCard
            :padding="false"
            class="pb-6 mb-4"
        >
            <template #header>
                <div class="px-6">
                    Smarta block
                </div>
            </template>
            <FTable
                :columns="columns"
                :options="{shadow: false, clickableRows: true}"
                :pagination="pagination"
                :rows="blocks"
                class="mb-8"
                paginated
                @row-clicked="handleRowClicked"
                @change-page="setPage"
                @sort="setSort"
            >
                <template #default="{ row: item, prop }">
                    <span v-if="prop == 'updated_at'">{{ item.updated_at | localTime }}</span>
                    <span
                        v-else-if="prop == 'edit'"
                        class="flex items-start justify-end space-x-5"
                    >
                        <RouterLink
                            :to="{name: 'smartBlocks.edit', params: { id: item.id }}"
                            class="flex items-center justify-end link"
                        >
                            <PenToSquareIcon
                                thin
                                class="w-6 h-6 text-gray-800"
                            />
                        </RouterLink>
                        <FConfirmDropdown
                            confirm-question="Vill du ta bort blocket?"
                            @confirmed="deleteSmartBlock(item)"
                        >
                            <TrashIcon
                                class="w-6 h-6 text-gray-800 hover:text-red-500"
                                thin
                            />
                        </FConfirmDropdown>
                    </span>
                </template>
            </FTable>
        </UiCard>
    </div>
</template>
<script>
import SmartBlock from '@/models/SmartBlock.js'
function defaultCreationObject () {
    return {
        name: ''
    }
}
export default {
    name: 'SmartBlocksIndex',
    data () {
        return {
            email: '',
            blocks: [],
            pagination: {},
            newSmartBlock: {
                name: ''
            },
            queryParams: {
                number: 50,
                page: 1
            },
            columns: [
                {
                    title: 'Namn',
                    key: 'name',
                    sortable: true
                },
                {
                    title: 'Uppdaterad',
                    key: 'updated_at',
                    sortable: true
                },
                {
                    title: '',
                    key: 'edit',
                    tdClasses: 'text-right'
                }
            ]
        }
    },
    activated () {
        this.fetchItems()
    },
    methods: {
        async fetchItems () {
            try {
                const payload = {
                    params: this.queryParams
                }
                const { data, meta } = await SmartBlock.index(payload)
                this.blocks = data
                this.pagination = meta.pagination
            } catch (error) {
                console.error(error)
            }
        },
        setSort (sort) {
            this.queryParams.sort = sort
            this.fetchItems()
        },
        setPage (pageNumber) {
            this.queryParams.page = pageNumber
            this.fetchItems()
        },
        async deleteSmartBlock (block) {
            try {
                await SmartBlock.destroy(block.id)
                this.$toast.success({ title: 'Det smarta blocket har tagits bort' })
                this.fetchItems()
            } catch (error) {
                console.error(error)
            }
        },
        async createSmartBlock () {
            try {
                const { data } = await SmartBlock.store(this.newSmartBlock)
                this.$toast.success({
                    title: 'Det smarta blocket har skapats!',
                    buttonText: 'Gå till blocket',
                    onClick: () => this.$router.push({ name: 'smartBlocks.edit', params: { id: data.id } })
                })
                this.$vfm.hide('createSmartBlockModal')
                this.resetCreateModal()
                this.fetchItems()
            } catch (error) {
                if (error.response.status === 422) {
                    this.$refs.observer.validate()
                }
                console.error(error)
            }
        },
        handleRowClicked (row) {
            this.$router.push({ name: 'smartBlocks.edit', params: { id: row.id } })
        },
        resetCreateModal () {
            setTimeout(() => {
                this.newSmartBlock = { ...defaultCreationObject() }
            }, 200)
        }
    }
}
</script>
