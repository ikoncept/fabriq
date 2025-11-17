<template>
    <div>
        <SortModal @updated="fetchContacts" />
        <UiSectionHeader class="mb-4">
            Kontakter
            <template #tools>
                <FButton
                    without-loader
                    :click="() => $vfm.show('sort-contact-modal')"
                    spinner-color="text-royal-500"
                    class="px-6 py-2.5 leading-none text-sm fabriq-btn btn-outline-royal"
                >
                    Sortera kontakter
                </FButton>
                <button
                    type="button"
                    class="fabriq-btn ml-4  btn-royal py-2.5 px-4 inline-flex items-center"
                    @click="$vfm.show('createContactModal')"
                >
                    Lägg till kontakt
                </button>
            </template>
        </UiSectionHeader>
        <CreateModal
            name="createContactModal"
            @validated="createContact"
            @closed="resetCreateModal"
            @opened="$refs.nameInput.$refs.input.focus()"
        >
            <template #title>
                Lägg till kontakt
            </template>
            <FInput
                ref="nameInput"
                v-model="creationObject.name"
                label="Namn"
                rules="required"
                class="mb-6"
                name="name"
            />
        </CreateModal>
        <UiCard
            :padding="false"
            class="pb-8"
        >
            <template #header>
                <div class="flex justify-between px-6">
                    <span class="inline-flex">
                        Kontakter
                    </span>
                </div>
            </template>

            <FTable
                :columns="columns"
                :options="{clickableRows: true, defaultSort: 'sortindex', sortDescending: false, shadow: false, search: true}"
                :pagination="pagination"
                :rows="contacts"
                :search-query="searchQuery"
                paginated
                @search="setSearch"
                @change-page="setPage"
                @row-clicked="editContact"
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
                                @perform-search="fetchContacts"
                                @clear-search="resetSearch"
                            />
                        </div>
                    </div>
                </template>
                <template #default="{ row: item, prop }">
                    <RouterLink
                        v-if="prop == 'name'"
                        class="flex items-center "
                        :to="{name: 'contacts.edit', params: { id: item.id }}"
                    >
                        <CircleUserIcon
                            v-if="! item.content.data.image"
                            thin
                            class="items-center w-6 h-6 mr-4"
                        />
                        <div v-else>
                            <UiImagePresenter
                                class="object-cover w-6 h-6 mr-4 rounded-full"
                                :image="item.content.data.image"
                            />
                            <!-- {{ item.image }} -->
                        </div>
                        {{ item.name }}
                    </RouterLink>
                    <span
                        v-else-if="prop == 'tags'"
                        class="flex space-x-2"
                    >
                        <UiBadge
                            v-for="(tag, index) in item.tags.data"
                            :key="index"
                        >{{ tag.name }}</UiBadge>
                    </span>
                    <span v-else-if="prop == 'sortindex'">
                        <UiBadge>{{ item.sortindex }}</UiBadge>
                    </span>
                    <span
                        v-else-if="prop == 'published'"
                        class="flex justify-center"
                    >
                        <CircleCheckIcon
                            v-if="item.published"
                            class="w-5 text-green-500"
                        />
                        <XMarkIcon
                            v-else
                            class="w-3 text-red-400"
                        />
                    </span>

                    <span
                        v-else-if="prop == 'edit'"
                        class="flex items-start justify-end space-x-5"
                    >
                        <RouterLink
                            :to="{name: 'contacts.edit', params: { id: item.id }}"
                            class="flex items-center justify-end link"
                        >
                            <PenToSquareIcon
                                thin
                                class="w-6 h-6 text-gray-800"
                            />
                        </RouterLink>
                        <FConfirmDropdown
                            confirm-question="Vill du ta bort kontakten?"
                            @confirmed="deleteContact(item)"
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
import Contact from '@/models/Contact.js'
import SortModal from './SortModal.vue'
function defaultCreationObject () {
    return {
        name: ''
    }
}
export default {
    name: 'ContactsIndex',
    components: { SortModal },
    data () {
        return {
            contacts: [],
            pagination: {},
            searchQuery: '',
            showCreate: false,
            creationObject: defaultCreationObject(),
            queryParams: {
                number: 50,
                sort: 'sortindex',
                'filter[search]': '',
                include: 'tags,content',
                append: 'image'
            },
            columns: [
                {
                    title: 'Namn',
                    key: 'name',
                    sortable: true,
                    tdClasses: 'font-medium'
                },
                {
                    title: 'E-post',
                    key: 'email',
                    sortable: true
                },
                {
                    title: 'Telefonnummer',
                    key: 'phone',
                    sortable: true
                },
                {
                    title: 'Sorteringsindex',
                    key: 'sortindex',
                    sortable: true
                },
                {
                    title: 'Visas',
                    key: 'published',
                    sortable: true
                },
                {
                    title: 'Taggar',
                    key: 'tags'
                    // thClasses: 'text-right'
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
        this.fetchContacts()
    },
    methods: {
        resetSearch () {
            this.queryParams['filter[search]'] = ''
            this.queryParams.page = 1
            this.fetchContacts()
        },
        async fetchContacts () {
            try {
                const payload = {
                    params: this.queryParams
                }
                const { data, meta } = await Contact.index(payload)
                this.contacts = data
                this.pagination = meta.pagination
            } catch (error) {
                console.error(error)
            }
        },
        async deleteContact (contact) {
            try {
                await Contact.destroy(contact.id)
                this.$toast.success({ title: 'Kontakten har raderats' })
                this.fetchContacts()
            } catch (error) {
                console.error(error)
            }
        },
        setSort (sort) {
            this.queryParams.sort = sort
            this.fetchContacts()
        },
        setPage (pageNumber) {
            this.queryParams.page = pageNumber
            this.fetchContacts()
        },
        editContact (row) {
            this.$router.push({ name: 'contacts.edit', params: { id: row.id } })
        },
        setSearch (value) {
            this.searchQuery = value
        },
        resetCreateModal () {
            setTimeout(() => {
                this.creationObject = { ...defaultCreationObject() }
            }, 200)
        },
        async createContact () {
            try {
                const { data } = await Contact.store(this.creationObject)
                this.$toast.success({
                    title: 'Kontakten har skapats!',
                    buttonText: 'Gå till kontakten',
                    onClick: () => this.$router.push({ name: 'contacts.edit', params: { id: data.id } })
                })
                this.$vfm.hide('createContactModal')
                this.resetCreateModal()
                this.fetchContacts()
            } catch (error) {
                console.error(error)
            }
        }
    }
}
</script>
<style>

input::-webkit-search-cancel-button {
    cursor: pointer;
    -webkit-appearance: none;
    height: 1.5rem;
    width: 1.5rem;
    background-repeat: no-repeat;
    background-image: url("data:image/svg+xml, %3Csvg aria-hidden='true' focusable='false' role='img' viewBox='0 0 384 512' xmlns='http://www.w3.org/2000/svg' %3E%3Cpath d='M347 411C341 418 331 418 325 411L192 279L59 411C53 418 43 418 37 411C30 405 30 395 37 389L169 256L37 123C30 117 30 107 37 101C43 94 53 94 59 101L192 233L325 101C331 94 341 94 347 101C354 107 354 117 347 123L215 256L347 389C354 395 354 405 347 411Z' fill='%239CA3AF' /%3E%3C/svg%3E");
}

</style>
