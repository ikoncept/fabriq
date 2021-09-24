<template>
    <div>
        <CreateModal :show="showCreate"
                     focus-ref="nameInput"
                     name="createItemModal"
                     @opened="$refs.nameInput.$refs.input.focus()"
                     @validated="createItem"
        >
            <template #title>
                Lägg till {{ swedishName }}
            </template>
            <FInput ref="nameInput"
                    v-model="newItem.name"
                    label="Namn"
                    rules="required"
                    class="mb-6"
                    name="name"
            />
        </CreateModal>
        <UiSectionHeader class="mb-4">
            {{ SwedishPluralName }}
            <template #tools>
                <button type="button"
                        class="fabriq-btn ml-10  btn-royal py-2.5 px-4 inline-flex items-center"
                        @click="$vfm.show('createItemModal')"
                >
                    Lägg till {{ swedishName }}
                </button>
            </template>
        </UiSectionHeader>
        <UiCard :padding="false"
                class="pb-4"
        >
            <template #header>
                <div class="flex justify-between px-6">
                    {{ SwedishPluralName }}
                </div>
            </template>
            <FTable
                :columns="columns"
                :pagination="pagination"
                :options="tableOptions"
                :rows="{{ pluralModelVariable }}"
                class="mb-8"
                paginated
                @change-page="setPage"
                @row-clicked="handleRowClick"
                @sort="setSort"
            >
                <template #default="{ row: item, prop }">
                    <span v-if="prop == 'edit'"
                        class="flex items-start justify-end space-x-5"
                    >
                        <RouterLink
                            :to="{name: '{{ pluralModelVariable }}.edit', params: {id: item.id }}"
                            class="flex items-center justify-end link"
                        >
                            <PenToSquareIcon class="w-6 h-6 text-gray-800 hover:text-gray-700"
                                                thin
                            />
                        </RouterLink>
                        <FConfirmDropdown confirm-question="Vill du ta bort denna sida?"
                                            @confirmed="deleteItem(item)"
                        >
                            <TrashIcon class="w-6 h-6 text-gray-800 hover:text-red-500"
                                        thin
                            />
                        </FConfirmDropdown>
                    </span>
                    <span v-else-if="prop == 'created_at'">{{ item.created_at | localTime }}</span>
                </template>
            </FTable>
        </UiCard>
    </div>
</template>
<script>
import {{ model }} from '~/models/{{ model }}'
function defaultCreationObject () {
    return {
        name: ''
    }
}
export default {
    name: '{{ pluralModel }}Index',
    data () {
        return {
            email: '',
            {{ pluralModelVariable }}: [],
            pagination: {},
            queryParams: {
                number: 20,
                page: 1,
                sort: '-created_at',
            },
            showCreate: false,
            newItem: {
                name: ''
            },
            tableOptions: {
                defaultSort: 'created_at',
                sortDescending: true,
                clickableRows: true,
                shadow: false
            },
            columns: [
                {
                    sortable: true,
                    title: 'Namn',
                    key: 'name'
                },
                {
                    title: 'Skapad',
                    key: 'created_at',
                    sortable: true
                },
                {
                    title: '',
                    key: 'edit',
                    tdClasses: 'text-right',
                    thClasses: 'text-right'
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
                const { data, meta } = await {{ model }}.index(payload)

                this.{{ pluralModelVariable }} = data
                this.pagination = meta.pagination
            } catch (error) {
                console.error(error)
            }
        },
        async deleteItem (article) {
            try {
                await {{ model }}.destroy(article.id)
                this.fetchItems()
            } catch (error) {
                console.error(error)
            }
        },
        async createItem () {
            try {
                const { data } = await {{ model }}.store(this.newItem)
                this.$toast.success({
                    title: 'Objektet har skapats!',
                    buttonText: 'Gå till objektet',
                    onClick: () => this.$router.push({ name: ' {{ pluralModelVariable }}.edit', params: { id: data.id } })
                })
                this.$vfm.hide('createItemModal')
                this.resetCreateModal()
                this.fetchItems()
            } catch (error) {
                if (error.response.status === 422) {
                    this.$refs.observer.validate()
                }
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
        handleRowClick (item) {
            this.$router.push({ name: '{{ pluralModelVariable }}.edit', params: { id: item.id } })
        },
        clearSearch () {
            this.queryParams['filter[search]'] = ''
            this.queryParams.page = 1
            this.fetchItems()
        },
        resetCreateModal () {
            this.newItem = { ...defaultCreationObject() }
        }
    }
}
</script>
