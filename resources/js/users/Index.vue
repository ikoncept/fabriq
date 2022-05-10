<template>
    <div>
        <CreateModal
            name="createUserModal"
            @opened="$refs.nameInput.$refs.input.focus()"
            @closed="resetCreateModal"
            @validated="createUser"
        >
            <template #title>
                Lägg till användare
            </template>
            <div class="grid grid-cols-2 gap-6">
                <FInput
                    ref="nameInput"
                    v-model="newUser.name"
                    label="Namn"
                    rules="required"
                    validation-mode="passive"
                />
                <FInput
                    v-model="newUser.email"
                    label="E-post"
                    rules="required|email"
                />
                <FSwitch
                    v-model="newUser.admin"
                    column-layout
                >
                    Admin
                </FSwitch>
                <hr class="col-span-2">
                <div class="col-span-2 mb-4">
                    <FSwitch
                        v-model="newUser.sendActivation"
                        help-text="Ett e-postmeddelande skickas med instruktioner för att aktivera kontot."
                        column-layout
                    >
                        Skicka aktivering
                    </FSwitch>
                </div>
            </div>
        </CreateModal>
        <UiSectionHeader class="mb-4">
            Användare
            <template #tools>
                <button
                    type="button"
                    class="fabriq-btn ml-10  btn-royal py-2.5 px-4 inline-flex items-center"
                    @click="$vfm.show('createUserModal')"
                >
                    Lägg till användare
                </button>
            </template>
        </UiSectionHeader>
        <UiCard
            :padding="false"
            class="pb-8"
        >
            <template #header>
                <span class="inline-flex px-6">
                    Användare
                </span>
            </template>

            <FTable
                :columns="columns"
                :options="{clickableRows: true, defaultSort: 'name', sortDescending: false, shadow: false, search: true}"
                :pagination="pagination"
                :rows="users"
                :search-query="searchQuery"
                paginated
                @search="setSearch"
                @change-page="setPage"
                @row-clicked="editUser"
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
                                @perform-search="fetchUsers"
                                @clear-search="resetSearch"
                            />
                        </div>
                    </div>
                </template>
                <template #default="{ row: item, prop }">
                    <span v-if="prop == 'updated_at'">{{ item.updated_at | localTime }}</span>
                    <span
                        v-else-if="prop == 'roles'"
                        class="space-x-2"
                    >
                        <UiBadge
                            v-for="role in item.roles.data"
                            :key="item.id + role.id"
                            color="royal"
                        >{{ role.display_name }}</UiBadge>
                    </span>
                    <span
                        v-else-if="prop == 'email_verified_at'"
                        @click.stop
                    >
                        <div
                            v-if="item.email_verified_at"
                            class="flex justify-end"
                        >
                            <CircleCheckIcon
                                class="w-5 text-green-500"
                            />
                        </div>
                        <div v-else>
                            <FButton
                                v-if="!item.invitation.data.is_valid"
                                :click="() => sendActivation(item.id)"
                                class="px-2 py-1 text-xs leading-none cursor-pointer link fabriq-button btn-royal whitespace-nowrap"
                            >Skicka aktivering</FButton>
                            <div v-else>
                                <div
                                    class="flex items-center justify-end space-x-2"
                                >

                                    <FButton
                                        size="xs"
                                        :click="() => sendActivation(item.id)"
                                        class="px-2 py-1 text-xs leading-none cursor-pointer link fabriq-button btn-royal whitespace-nowrap"
                                    >Skicka igen </FButton>
                                    <FButton
                                        :click="() => cancelActivation(item.id)"
                                        class="px-2 py-1 text-xs leading-none border border-red-500 cursor-pointer link fabriq-button btn-outline-red whitespace-nowrap"
                                        spinner-color="text-red-400"
                                    >Avbryt<FButton /></fbutton></div>
                                <div class="mt-1 text-xs text-neutral-500">Skickad {{ item.invitation.data.created_at | localTime }}</div>
                            </div>
                        </div>
                    </span>
                    <span
                        v-else-if="prop == 'edit'"
                        class="flex items-start justify-end space-x-5"
                    >
                        <RouterLink
                            :to="{name: 'users.edit', params: { id: item.id }}"
                            class="flex items-center justify-end link"
                        >
                            <PenToSquareIcon
                                thin
                                class="w-6 h-6 text-gray-800"
                            />
                        </RouterLink>
                        <FConfirmDropdown
                            confirm-question="Vill du ta bort användaren?"
                            @confirmed="deleteUser(item)"
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
function newUserObject() {
    return {
        name: '',
        email: '',
        admin: false,
        sendActivation: true,
        role_list: []
    }
}
import User from '~/models/User'
import Invitation from '~/models/Invitation'
export default {
    name: 'UsersIndex',
    data () {
        return {
            users: [],
            pagination: {},
            searchQuery: '',
            queryParams: {
                number: 50,
                include: 'roles,invitation',
                sort: 'name',
                'filter[search]': ''
            },
            newUser: {...newUserObject()},
            columns: [
                {
                    title: 'Namn',
                    key: 'name',
                    sortable: true
                },
                {
                    title: 'E-post',
                    key: 'email',
                    sortable: true,
                    tdClasses: 'font-medium'
                },
                {
                    title: 'Roller',
                    key: 'roles',
                    sortable: false
                },
                {
                    title: 'Uppdaterad',
                    key: 'updated_at',
                    sortable: true
                },
                {
                    title: 'Aktiverad',
                    key: 'email_verified_at',
                    sortable: false,
                    tdClasses: 'text-right',
                    thClasses: 'text-right',
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
        this.fetchUsers()
    },
    methods: {
        resetSearch () {
            this.queryParams['filter[search]'] = ''
            this.queryParams.page = 1
            this.fetchUsers()
        },
        async fetchUsers () {
            try {
                const payload = {
                    params: this.queryParams
                }
                const { data, meta } = await User.index(payload)
                this.users = data
                this.pagination = meta.pagination
            } catch (error) {
                console.error(error)
            }
        },
        async deleteUser (user) {
            try {
                await User.destroy(user.id)
                this.$toast.success({ title: 'Användaren har raderats' })
                this.fetchUsers()
            } catch (error) {
                console.error(error)
            }
        },
        setSort (sort) {
            this.queryParams.sort = sort
            this.fetchUsers()
        },
        setPage (pageNumber) {
            this.queryParams.page = pageNumber
            this.fetchUsers()
        },
        editUser (row) {
            this.$router.push({ name: 'users.edit', params: { id: row.id } })
        },
        setSearch (value) {
            this.searchQuery = value
        },

        async sendActivation (id) {
            await Invitation.store(id)
            // await this.$store.dispatch('Invitation/store', { userId: id })
            this.$toast.success({ title: 'Aktivering skickad!' })
            this.fetchUsers()
        },
        async cancelActivation (id) {
            try {
                await Invitation.destroy(id)
                this.fetchUsers()
                this.$toast.success({ title: 'Inbjudan har tagits bort' })
            } catch (error) {
                console.error(error)
            }
        },
        async createUser () {
            try {
                const shouldSendActivation = this.newUser.sendActivation
                if(this.newUser.admin) {
                    this.newUser.role_list.push('admin')
                }
                const { data } = await User.store(this.newUser)
                this.$toast.success({
                    title: 'Användaren har skapats!',
                    buttonText: 'Gå till användaren',
                    onClick: () => this.$router.push({ name: 'users.edit', params: { id: data.id } })
                })
                this.$vfm.hide('createUserModal')
                if(shouldSendActivation) {
                    await this.sendActivation(data.id)
                }
                this.resetCreateModal()
                this.fetchUsers()
            } catch (error) {
                console.error(error)
            }
        },
        resetCreateModal () {
            setTimeout(() => {
                this.newUser = { ...newUserObject() }
            }, 200)
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
