<template>
    <div>
        <UiSectionHeader class="mb-4">
            Menyer
        </UiSectionHeader>
        <UiCard :padding="false"
                class="pb-6"
        >
            <template #header>
                <div class="px-6">
                    Menyer
                </div>
            </template>
            <FTable
                :columns="columns"
                :options="tableOptions"
                :rows="menus"
                @row-clicked="handleRowClicked"
            >
                <template #default="{ row: menu, prop }">
                    <span v-if="prop == 'edit'"
                          class="flex items-start justify-end space-x-5"
                    >
                        <RouterLink
                            :to="{name: 'menus.edit', params: {id: menu.id }}"
                            class="flex items-center justify-end link"
                        >
                            <PenToSquareIcon thin
                                             class="w-6 h-6 text-gray-800"
                            />
                        </RouterLink>
                    <!-- <f-confirm-dropdown confirm-question="Vill du ta bort menyn?"
                                            @confirmed="deleteUser(item)"
                        >
                            <trash-icon class="w-6 h-6 text-gray-800 hover:text-red-500"
                                        thin
                            />
                        </f-confirm-dropdown> -->
                    </span>
                </template>
            </FTable>
        </UiCard>
    </div>
</template>
<script>
import Menu from '~/models/Menu'
export default {
    name: 'MenusIndex',
    data () {
        return {
            menus: [],
            tableOptions: {
                emptyText: 'Här var det tomt!',
                clickableRows: true,
                shadow: false
            },
            columns: [
                {
                    title: '',
                    key: 'name',
                    tdClasses: 'font-semibold'
                },
                {
                    title: '',
                    key: 'edit',
                    thClasses: 'text-right',
                    tdClasses: 'text-right'
                }

            ],
            queryParams: {
                number: 40,
                page: 1
            }
        }
    },
    activated () {
        this.fetchMenus()
    },
    methods: {
        handleRowClicked (row) {
            this.$router.push({ name: 'menus.edit', params: { id: row.id } })
        },
        async fetchMenus () {
            try {
                const payload = {
                    params: this.queryParams
                }
                const { data } = await Menu.index(payload)
                this.menus = data
            } catch (error) {
                console.error(error)
            }
        }
    }
}
</script>
