<template>
    <div>
        <CreateModal
            :show="showCreate"
            focus-ref="nameInput"
            name="createArticleModal"
            @opened="$refs.nameInput.$refs.input.focus()"
            @validated="createArticle"
        >
            <template #title>
                Lägg till nyhet
            </template>
            <FInput
                ref="nameInput"
                v-model="newArticle.name"
                label="Namn"
                rules="required"
                class="mb-6"
                name="name"
            />
        </CreateModal>
        <UiSectionHeader class="mb-4 lookie">
            Nyheter
            <template #tools>
                <button
                    type="button"
                    class="fabriq-btn ml-10  btn-royal py-2.5 px-4 inline-flex items-center"
                    @click="$vfm.show('createArticleModal')"
                >
                    Lägg till nyhet
                </button>
            </template>
        </UiSectionHeader>
        <UiCard
            :padding="false"
            class="pb-4"
        >
            <template #header>
                <div class="flex justify-between px-6">
                    Nyheter
                </div>
            </template>
            <FTable
                :columns="columns"
                :pagination="pagination"
                :options="tableOptions"
                :rows="articles"
                class="mb-8"
                paginated
                @change-page="setPage"
                @row-clicked="handleRowClick"
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
                                @perform-search="fetchArticles"
                                @clear-search="clearSearch"
                            />
                        </div>
                    </div>
                </template>
                <template #default="{ row: item, prop }">
                    <span v-if="prop == 'updated_at'">{{ item.updated_at | localTime }}</span>
                    <span v-else-if="prop == 'publishes_at'">
                        <span v-if="item.is_published">
                            <UiBadge color="gold">{{ item.publishes_at | localTime }}</UiBadge>
                            <div
                                v-if="item.has_unpublished_time"
                                class="mt-1 text-xs italic text-gray-400"
                            >Avpubliceras {{ item.unpublishes_at | localTime }}</div>
                        </span>
                        <span v-else-if="! item.has_unpublished_time">
                            <UiBadge v-if="item.publishes_at">Publiceras {{ item.publishes_at | localTime }}</UiBadge>
                            <span
                                v-else
                                class="text-xs italic text-gray-400"
                            >Inte publicerad</span>
                        </span>
                        <span
                            v-else
                            class="text-xs italic text-gray-400"
                        >Inte publicerad</span>
                    </span>
                    <span
                        v-else-if="prop == 'edit'"
                        class="flex items-start justify-end space-x-5"
                    >
                        <RouterLink
                            :to="{name: 'articles.edit', params: {id: item.id }}"
                            class="flex items-center justify-end link"
                        >
                            <PenToSquareIcon
                                class="w-6 h-6 text-gray-800 hover:text-gray-700"
                                thin
                            />
                        </RouterLink>
                        <FConfirmDropdown
                            confirm-question="Vill du ta bort denna sida?"
                            @confirmed="deleteArticle(item)"
                        >
                            <TrashIcon
                                class="w-6 h-6 text-gray-800 hover:text-red-500"
                                thin
                            />
                        </FConfirmDropdown>
                    </span>
                    <!-- <span v-else-if="prop == 'delete'" /> -->
                </template>
            </FTable>
        </UiCard>
    </div>
</template>
<script>
import Article from '~/models/Article'
function defaultCreationObject () {
    return {
        name: ''
    }
}
export default {
    name: 'ArticlesIndex',
    data () {
        return {
            email: '',
            articles: [],
            pagination: {},
            queryParams: {
                number: 20,
                page: 1,
                sort: '-publishes_at',
                'filter[search]': ''
            },
            showCreate: false,
            newArticle: {
                name: ''
            },
            tableOptions: {
                defaultSort: 'publishes_at',
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
                    title: 'Publiceringstid',
                    key: 'publishes_at',
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
                    tdClasses: 'text-right',
                    thClasses: 'text-right'
                }
            ]
        }
    },
    activated () {
        this.fetchArticles()
    },
    methods: {
        async fetchArticles () {
            try {
                const payload = {
                    params: this.queryParams
                }
                const { data, meta } = await Article.index(payload)

                this.articles = data
                this.pagination = meta.pagination
            } catch (error) {
                console.error(error)
            }
        },
        async deleteArticle (article) {
            try {
                await Article.destroy(article.id)
                this.fetchArticles()
            } catch (error) {
                console.error(error)
            }
        },
        async createArticle () {
            try {
                const { data } = await Article.store(this.newArticle)
                this.$toast.success({
                    title: 'Nyheten har skapats!',
                    buttonText: 'Gå till nyheten',
                    onClick: () => this.$router.push({ name: 'articles.edit', params: { id: data.id } })
                })
                this.$vfm.hide('createArticleModal')
                this.resetCreateModal()
                this.fetchArticles()
            } catch (error) {
                if (error.response.status === 422) {
                    this.$refs.observer.validate()
                }
                console.error(error)
            }
        },
        setSort (sort) {
            this.queryParams.sort = sort
            this.fetchArticles()
        },
        setPage (pageNumber) {
            this.queryParams.page = pageNumber
            this.fetchArticles()
        },
        handleRowClick (item) {
            this.$router.push({ name: 'articles.edit', params: { id: item.id } })
        },
        clearSearch () {
            this.queryParams['filter[search]'] = ''
            this.queryParams.page = 1
            this.fetchArticles()
        },
        resetCreateModal () {
            setTimeout(() => {
                this.newArticle = { ...defaultCreationObject() }
            }, 200)
        }
    }
}
</script>
