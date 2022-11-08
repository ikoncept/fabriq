<template>
    <div>
        <UiSectionHeader class="mb-4">
            Sidor
            <template #tools>
                <button
                    type="button"
                    class="fabriq-btn ml-10  btn-royal py-2.5 px-4 inline-flex items-center"
                    @click="$vfm.show('createPageModal')"
                >
                    Lägg till sida
                </button>
            </template>
        </UiSectionHeader>
        <CreateModal
            name="createPageModal"
            width="max-w-xl"
            @before-open="fetchTemplates"
            @closed="resetCreateModal"
            @validated="createPage"
            @opened="$refs.nameInput.$refs.input.focus()"
        >
            <template #title>
                Lägg till sida
            </template>
            <FInput
                ref="nameInput"
                v-model="newPage.name"
                label="Namn"
                rules="required"
                class="mb-6"
                name="name"
            />
            <FSelect
                v-model="newPage.template"
                name="template_id"
                :options="templates"
                placeholder="Vänligen välj"
                option-label="name"
                class="mb-6"
                rules="required"
                value-key="id"
                label="Sidtyp"
                :reduce-fn="item => item"
            />
        </CreateModal>
        <UiCard
            class="pb-4"
        >
            <template #header>
                <div class="flex justify-between ">
                    Sidor
                </div>
            </template>
            <VueNestable
                v-model="pageTree"
                @change="updateTree"
            >
                <template #placeholder>
                    <div class="flex items-center justify-center h-48 border-2 border-dashed rounded border-royal-200">
                        <div class="flex flex-col items-center">
                            <div class="mb-4 text-xl font-light">
                                Inga sidor har lagts till ännu
                            </div>
                            <button
                                class="flex items-center text-sm link"
                                @click="$vfm.show('createPageModal')"
                            >
                                <PlusIcon class="w-5 h-5 mr-2" />Lägg till sida
                            </button>
                        </div>
                    </div>
                </template>
                <template slot-scope="{ item }">
                    <div class="flex items-center justify-between p-2 text-sm border border-gray-300 rounded group">
                        <div class="flex items-center font-semibold text-gray-700">
                            <VueNestableHandle
                                :item="item"
                                class="px-2 -mx-2"
                            >
                                <GripVerticalIcon class="w-4 h-4 ml-1 mr-2 text-gray-300" />
                            </VueNestableHandle>
                            <RouterLink :to="{ name: 'pages.edit', params: {id: item.id } }">
                                {{ item.name }}
                            </RouterLink>
                        </div>
                        <div class="w-64" />

                        <div>
                            <div class="flex items-center justify-end space-x-4 ">
                                <div class="font-medium text-gray-400">
                                    <UiBadge class="mr-8">
                                        {{ item.template.name }}
                                    </UiBadge>
                                </div>
                                <button
                                    v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Klona sida' }"
                                    class="focus:outline-none"
                                    @click.stop="clonePage(item)"
                                >
                                    <CloneIcon
                                        thin
                                        class="h-6"
                                    />
                                </button>
                                <RouterLink
                                    :to="{name: 'pages.edit', params: {id: item.id }}"
                                    class="flex items-center justify-end link"
                                >
                                    <PenToSquareIcon
                                        thin
                                        class="w-6 h-6 text-gray-800"
                                    />
                                </RouterLink>
                                <FConfirmDropdown
                                    confirm-question="Vill du ta bort denna sida?"
                                    @confirmed="deletePage(item)"
                                >
                                    <TrashIcon
                                        class="w-6 h-6 mt-1 text-gray-800 hover:text-red-500"
                                        thin
                                    />
                                </FConfirmDropdown>
                            </div>
                        </div>
                    </div>
                </template>
            </VueNestable>
            <FTable
                v-if="false"
                :columns="columns"
                :options="tableOptions"
                :pagination="pagination"
                :rows="pages"
                class="mb-8"
                paginated
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
                                @perform-search="fetchPages"
                                @clear-search="clearSearch"
                            />
                        </div>
                    </div>
                </template>
                <template #default="{ row: page, prop }">
                    <span v-if="prop == 'updated_at'">{{ page.updated_at | localTime }}</span>
                    <span v-else-if="prop == 'template_name'">
                        <UiBadge>
                            {{ page.template.data.name }}
                        </UiBadge>
                    </span>
                    <span
                        v-else-if="prop == 'edit'"
                        class="flex items-start justify-end space-x-5"
                    >
                        <RouterLink
                            :to="{name: 'pages.edit', params: {id: page.id }}"
                            class="flex items-center justify-end link"
                        >
                            <PenToSquareIcon
                                thin
                                class="w-6 h-6 text-gray-800"
                            />
                        </RouterLink>
                        <FConfirmDropdown
                            confirm-question="Vill du ta bort denna sida?"
                            @confirmed="deletePage(page)"
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
import Page from '@/models/Page.js'
import PageTree from '@/models/PageTree.js'
import Template from '@/models/Template.js'
import VueNestable from '@/plugins/vue-nestable/VueNestable.vue'
import VueNestableHandle from '@/plugins/vue-nestable/VueNestableHandle.vue'
import '@/plugins/vue-nestable/vue-nestable.css';
function defaultCreationObject () {
    return {
        name: '',
        template: null
    }
}
export default {
    name: 'PagesIndex',
    components: { VueNestable, VueNestableHandle },
    data () {
        return {
            pages: [],
            pageTree: [],
            pagination: {},
            pageToClone: {},
            newPage: {
                name: '',
                template: null
            },
            templates: [],
            queryParams: {
                number: 10,
                page: 1,
                include: 'template',
                'filter[search]': '',
                sort: 'name'
            },
            tableOptions: {
                clickableRows: false,
                defaultSort: 'name',
                sortDescending: false,
                shadow: false
            },
            columns: [
                {
                    sortable: true,
                    title: 'ID',
                    key: 'id',
                    tdClasses: 'w-20'
                },
                {
                    title: 'Namn',
                    key: 'name',
                    tdClasses: 'text-gray-900 font-medium',
                    sortable: true
                },
                {
                    title: 'Sidmall',
                    key: 'template_name'
                },
                {
                    title: 'Uppdaterad',
                    key: 'updated_at',
                    sortable: true
                },
                {
                    title: '',
                    key: 'edit',
                    tdClasses: 'text-right '
                }
            ],
            localizedContent: {}
        }
    },
    activated () {
        // this.fetchPages()
        this.fetchPageTree()
    },
    methods: {
        setSort (sort) {
            this.queryParams.sort = sort
            this.fetchPages()
        },
        setPage (pageNumber) {
            this.queryParams.page = pageNumber
            this.fetchPages()
        },
        async fetchPageTree () {
            try {
                const { data } = await PageTree.index()
                this.pageTree = data
            } catch (error) {
                console.error(error)
            }
        },
        async fetchPages () {
            try {
                const payload = {
                    params: this.queryParams
                }
                const { data, meta } = await Page.index(payload)
                this.pages = data
                this.pagination = meta.pagination
            } catch (error) {
                console.log(error)
            }
        },
        async clonePage(page, parameters = {}) {
            await this.fetchPage(page.id)

            let localizedContent = {}
            Object.keys(this.pageToClone.localizedContent.data).forEach((item) => {
                localizedContent[item] = { ...this.pageToClone.localizedContent.data[item].content }
            })
            const payload = {
                localizedContent: localizedContent,
            }
            await Page.clone(page.id, {...payload, ...parameters})
            this.$toast.success({ title: 'Sidan har klonats!' })
            this.fetchPageTree()
        },
        async fetchPage (id) {
            const payload = {
                params: {
                    include: 'localizedContent',
                    locale: 'all',
                    append: 'paths'
                }
            }
            try {
                const { data } = await Page.show(id, payload)
                this.pageToClone = data
                console.log(this.pageToClone)
                // this.page = data
                // this.fields = data.template.data.fields
                // this.groupedFields = data.template.data.groupedFields.data
                // localizedContent = { ...data.localizedContent.data }
                // console.log(localizedContent)

                // Object.keys(localizedContent).forEach((item) => {
                //     this.$set(this.localizedContent, item, { ...localizedContent[item].content })
                // })
            } catch (error) {
                console.error(error)
            }
        },
        async deletePage (page) {
            if (page.children.length > 0) {
                this.$toast.info({ title: 'Kunde inte radera sidan', message: 'Sidan har sidor under sig, radera eller flytta dessa först' })
                return false
            }
            try {
                await Page.destroy(page.id)
                this.$toast.success({ title: 'Sidan har raderats ' })
                this.fetchPageTree()
            } catch (error) {
                console.error(error)
            }
        },
        async fetchTemplates () {
            try {
                const payload = {
                    params: {
                        'filter[type]': 'page'
                    }
                }
                const { data } = await Template.index(payload)
                this.templates = data
            } catch (error) {
                console.error(error)
            }
        },
        // async createPage () {
        //     try {
        //         const { data } = await Page.store(this.newPage)
        //         this.$toast.success({
        //             title: 'Sidan har skapats!',
        //             buttonText: 'Gå till sidan',
        //             onClick: () => this.$router.push({ name: 'pages.edit', params: { id: data.id } })
        //         })
        //         this.$vfm.hide('createPageModal')
        //         this.resetCreateModal()
        //         this.fetchPageTree()
        //     } catch (error) {
        //         console.error(error)
        //         if (error.response.status === 422) {
        //             this.$refs.observer.validate()
        //         }
        //     }
        // },
        async createPage () {
            try {
                if(this.newPage.template.locked) {
                    await this.clonePage({id: this.newPage.template.source_model_id}, {name: this.newPage.name})
                    this.$vfm.hide('createPageModal')
                    this.resetCreateModal()
                    return
                }
                const { data } = await Page.store(this.newPage)
                this.$toast.success({
                    title: 'Sidan har skapats!',
                    buttonText: 'Gå till sidan',
                    onClick: () => this.$router.push({ name: 'pages.edit', params: { id: data.id } })
                })
                this.$vfm.hide('createPageModal')
                this.resetCreateModal()
                this.fetchPageTree()
            } catch (error) {
                console.error(error)
                if (error.response.status === 422) {
                    this.$refs.observer.validate()
                }
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
            this.pageTree.forEach((item, index) => {
                item.sortindex = index * 10
                items.push(item)
                items = [...items, ...this.getSubItems(item)]
            })

            try {
                const payload = {
                    tree: this.pageTree
                }
                await PageTree.update(payload)
                this.fetchPageTree()
            } catch (error) {
                console.error(error)
            }
        },
        clearSearch () {
            this.queryParams['filter[search]'] = ''
            this.queryParams.page = 1
            this.fetchPages()
        },
        resetCreateModal () {
            setTimeout(() => {
                this.newPage = { ...defaultCreationObject() }
            }, 200)
        }
    }
}
</script>
