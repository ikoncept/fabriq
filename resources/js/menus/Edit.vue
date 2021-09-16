<template>
    <div>
        <MenuItemModal
            :is-creating="isCreating"
            @created="fetchTree"
            @updated="fetchTree"
        />
        <UiSectionHeader>
            Redigera meny
            <template #subtitle>
                {{ menu.name }}
            </template>
            <template #tools>
                <div class="space-x-4 whitespace-nowrap">
                    <RouterLink
                        :to="{name: 'menus.index'}"
                        class="inline-flex px-6 py-2.5 my-0 leading-none fabriq-btn btn-link"
                    >
                        Avbryt
                    </RouterLink>
                    <FButton
                        :click="updateTree"
                        class="px-6 py-2.5 leading-none fabriq-btn btn-royal"
                    >
                        Spara
                    </FButton>
                </div>
            </template>
        </UiSectionHeader>
        <UiCard>
            <template #header>
                <div class="flex justify-between flex-1">
                    Menypunkter
                    <FButton :click="createMenuItem"
                             class="text-sm"
                             text-button
                             without-loader
                    >
                        <PlusIcon class="w-4 h-4 mr-1" />Lägg till menypunkt
                    </FButton>
                </div>
            </template>
            <div class>
                <VueNestable v-model="tree">
                    <template #placeholder>
                        <div class="flex items-center justify-center h-48 border-2 border-dashed rounded border-royal-200">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 text-xl font-light">
                                    Inga menypunkter har lagts till ännu
                                </div>
                                <button class="flex items-center text-sm link"
                                        @click="createMenuItem"
                                >
                                    <PlusIcon class="w-5 h-5 mr-2" />Lägg till menypunkt
                                </button>
                            </div>
                        </div>
                    </template>
                    <template slot-scope="{ item }">
                        <div
                            v-show="! deleteQueue.includes(item.id)"
                            class="flex items-center h-12 p-2 border border-gray-300 rounded group"
                        >
                            <VueNestableHandle :item="item"
                                               class="px-2 -mx-2"
                            >
                                <GripVerticalIcon class="w-4 h-4 ml-1 mr-2 text-gray-300" />
                            </VueNestableHandle>

                            <!-- Content -->
                            <div class="flex flex-1 text-sm font-semibold text-gray-700">
                                <span v-if="item.type === 'internal'"
                                      class="cursor-pointer"
                                      @click="editMenuItem(item)"
                                >
                                    {{ item.page.name }}
                                </span>
                                <span v-if="item.type === 'external'">{{ item.title }}</span>
                                <span class="ml-4 mr-auto">
                                    <!-- <external-link-icon
                                        v-if="item.is_external"
                                        class="w-4 h-4"
                                        title="Extern url"
                                    /> -->
                                    <UiBadge v-if="item.type === 'external'">
                                        <ExternalLinkIcon class="w-3 h-3 mr-1" /> Extern länk
                                    </UiBadge>
                                </span>
                            </div>
                            <div class="flex md:space-x-6">
                                <button
                                    class="flex text-sm focus:outline-none "
                                    @click="editMenuItem(item)"
                                >
                                    <PenToSquareIcon class="w-6 h-6"
                                                     thin
                                    />
                                </button>
                                <div class="pr-2">
                                    <FConfirmDropdown confirm-question="Vill du ta bort menypunkten?"
                                                      @confirmed="deleteConfirmed(item.id)"
                                    >
                                        <TrashIcon class="w-6 h-6"
                                                   thin
                                        />
                                    </FConfirmDropdown>
                                </div>
                            </div>
                        </div>
                    </template>
                </VueNestable>
            </div>
        </UiCard>
    </div>
</template>
<script>
import MenuItemModal from '~/menus/MenuItemModal'
import Menu from '~/models/Menu'
import MenuItem from '~/models/MenuItem'
export default {
    name: 'MenusEdit',
    components: { MenuItemModal },
    beforeRouteLeave (from, to, next) {
        this.$vfm.hide('menu-item-modal')
        this.$destroy()
        next()
    },
    data () {
        return {
            menu: {},
            id: 0,
            tree: [],
            show: false,
            activeItem: {},
            isCreating: false,
            deleteQueue: [],
            confirm_id: 0
        }
    },
    activated () {
        this.id = this.$route.params.id
        this.fetchMenu()
        this.fetchTree()
    },
    methods: {
        async fetchTree () {
            try {
                const { data } = await Menu.showTree(this.id)
                this.tree = data
            } catch (error) {
                console.error(error)
            }
        },
        async fetchMenu () {
            try {
                const payload = {
                    include: 'menuItems'
                }
                const { data } = await Menu.show(this.id, payload)
                this.menu = data
            } catch (error) {
                console.error(error)
            }
        },
        getSubItems (item, items = []) {
            if (item.children) {
                item.children.forEach((index_, index) => {
                    index_.parent_id = item.id
                    index_.sortindex = index * 10
                    items.push(index_)
                    this.getSubItems(index_, items)
                })
            }

            return items
        },
        async updateTree () {
            let items = []
            this.tree.forEach((item, index) => {
                item.sortindex = index * 10
                items.push(item)
                items = [...items, ...this.getSubItems(item)]
            })

            try {
                const payload = {
                    tree: this.tree
                }
                await Menu.updateTree(this.id, payload)
                this.$toast.success({ title: 'Menyn har uppdaterats!' })
                this.fetchTree()
            } catch (error) {
                console.error(error)
            }
        },
        async editMenuItem (item) {
            this.isCreating = false
            this.$vfm.show('menu-item-modal', item.id)
            // try {
            //     const payload = {
            //         params: {
            //             include: 'content'
            //         }
            //     }
            //     const { data } = await MenuItem.show(item.id, payload)
            //     this.activeItem = data
            // } catch (error) {
            //     console.error(error)
            // }
        },
        createMenuItem () {
            this.activeItem = {}
            this.isCreating = true
            this.$vfm.show('menu-item-modal')
        },
        async deleteConfirmed (id) {
            try {
                await MenuItem.destroy(id)
                this.$toast.success({ title: 'Menypunkten har raderats' })
                this.fetchTree()
            } catch (error) {
                console.error(error)
            }
        }
    }
}
</script>
