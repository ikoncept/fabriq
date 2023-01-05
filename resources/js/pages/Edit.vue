<template>
    <div>
        <RefreshObjectModal>
            <template #title>
                Sidan har uppdaterats
            </template>
            <template #default="{ params }">
                <p
                    v-if="params.model"
                    class="text-sm"
                >
                    Sidan har uppdaterats av <span class="font-medium">{{ params.model.updatedByName }}</span>, därför behöver sidan laddas om.
                </p>
            </template>
        </RefreshObjectModal>
        <UiSectionHeader>
            Redigera sida
            <template #subtitle>
                <div class="flex items-end space-x-4">
                    <span>
                        {{ page.name }}
                    </span>
                    <span v-if="devMode && page.locked">
                        <RouterLink
                            :to="{name: 'pages.edit', params: {id: page.template.data.source_model_id}}"
                            class="flex items-center text-sm link"
                            @click="showBlockTypeModal"
                        >
                            Redigera mall
                        </RouterLink>
                    </span>
                </div>
            </template>
            <template #tools>
                <div :class="{'opacity-70 pointer-events-none': !currentUserIsFirstIn }">
                    <div class="flex flex-wrap space-x-4 whitespace-nowrap">
                        <FButton
                            class="px-6 py-2.5 leading-none text-sm fabriq-btn btn-link"
                            back-button="pages.index"
                        >
                            Avbryt
                        </FButton>
                        <FButton
                            :click="previewPage"
                            spinner-color="text-royal-500"
                            class="px-6 py-2.5 leading-none text-sm fabriq-btn btn-outline-royal"
                        >
                            Förhandsgranska
                        </FButton>
                        <FButton
                            :click="updateContent"
                            spinner-color="text-royal-500"
                            class="px-6 py-2.5 leading-none text-sm fabriq-btn btn-outline-royal"
                        >
                            Spara utkast
                        </FButton>
                        <FButton
                            :click="publishPage"
                            class="px-6 py-2.5 leading-none text-sm fabriq-btn btn-royal"
                        >
                            Spara & publicera
                        </FButton>
                    </div>
                </div>
            </template>
        </UiSectionHeader>
        <div class="flex justify-end">
            <div class="absolute mt-2">
                <PresenceInfo />
            </div>
        </div>
        <FTabs
            v-if="Object.keys(locales).length > 0"
            @change="setLanguage"
        >
            <FTab
                v-for="(locale, lIndex) in locales"
                :key="lIndex"
                :value-key="lIndex"
                :title="locale.native"
            >
                <UiCard
                    collapsible
                    group="pages-ettings"
                    sync-groups
                >
                    <template #header>
                        Sidinställningar
                    </template>
                    <div class="grid grid-cols-3 gap-x-6 gap-y-6">
                        <FInput
                            v-model="page.name"
                            label="Namn"
                            name="name"
                        />
                        <FInput
                            v-if="Object.keys(localizedContent).length > 0"
                            v-model="localizedContent[activeLocale].page_title"
                            name="*.page_title"
                            label="Sidtitel"
                            help-text="Visas utåt i menyer"
                        />
                        <FInput
                            v-model="page.template.data.name"
                            label="Sidtyp"
                            name="template.data.name"
                            disabled
                        />
                        <PagePaths
                            :paths="paths"
                            class="flex col-span-3 space-x-6 lg:col-span-2"
                        />
                    </div>
                </UiCard>
                <div
                    class="grid grid-cols-4 space-x-6"
                >
                    <div class="col-span-4">
                        <div
                            v-for="(fieldGroup, index) in groupedFields"
                            :key="'g' + index"
                        >
                            <UiCard
                                v-if="! repeaterKeys.includes(index)"
                                :group="index"
                                sync-groups
                                collapsible
                            >
                                <template #header>
                                    <h3 class>
                                        <span v-if="index === 'meta'">Meta-fält</span>
                                        <span v-else-if="index === 'main_content'">Sidtypsegenskaper</span>
                                        <span v-else>{{ index }}</span>
                                    </h3>
                                </template>

                                <div class="grid grid-cols-12 gap-x-6">
                                    <div
                                        v-for="(field, fieldIndex) in fieldGroup"
                                        :key="'f' + fieldIndex"
                                        :class="[field.options ? field.options.classes : 'col-span-12']"
                                    >
                                        <FInput
                                            v-if="field.type == 'text'"
                                            v-model="localizedContent[lIndex][field.key]"
                                            :label="field.name"
                                            :name="field.key"
                                            class="mb-6"
                                        />
                                        <div v-else-if="field.type == 'textarea'">
                                            <FInput
                                                v-model="localizedContent[lIndex][field.key]"
                                                :label="field.name"
                                                class="mb-6"
                                                :name="field.key"
                                                textarea
                                            />
                                        </div>
                                        <div
                                            v-else-if="field.type == 'html'"
                                            class="mb-6"
                                        >
                                            <FEditor
                                                v-model="localizedContent[lIndex][field.key]"
                                                :label="field.name"
                                                :name="field.key"
                                            />
                                        </div>
                                        <div
                                            v-else-if="field.type == 'image'"
                                            class="mb-6"
                                        >
                                            <FImageInput
                                                v-model="localizedContent[lIndex][field.key]"
                                                :label="field.name"
                                                :group="field.options.group"
                                                :name="field.key"
                                                :field-key="field.key"
                                                :model-id="page.id"
                                            />
                                        </div>
                                        <div
                                            v-else-if="field.type == 'video'"
                                            class="mb-6"
                                        >
                                            <FVideoInput
                                                v-model="localizedContent[lIndex][field.key]"
                                                :label="field.name"
                                                :name="field.key"
                                                :group="field.options.group"
                                                :field-key="field.key"
                                                :model-id="page.id"
                                            />
                                        </div>
                                        <div
                                            v-else-if="field.type == 'button'"
                                            class="mb-6"
                                        >
                                            <div>
                                                <FLabel :name="field.key">
                                                    Knapp
                                                </FLabel>
                                            </div>
                                            <FButtonItem
                                                v-model="localizedContent[lIndex][field.key]"
                                                class="col-span-12 lg:col-span-8"
                                            />
                                        </div>
                                        <div
                                            v-else-if="field.type == 'switch'"
                                            class="mb-6"
                                        >
                                            <FSwitch
                                                v-model="localizedContent[lIndex][field.key]"
                                                column-layout
                                            >
                                                {{ field.name }}
                                            </FSwitch>
                                        </div>
                                    </div>
                                </div>
                            </UiCard>
                            <div v-else>
                                <BlockList
                                    v-if="activeLocale === lIndex"
                                    :key="activeLocale + 'b'"
                                    v-model="localizedContent[activeLocale].boxes"
                                    :locale="lIndex"
                                    :page="page"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </FTab>
        </FTabs>
    </div>
</template>

<script>
import BlockList from '@/blocks/BlockList.vue'
import RefreshObjectModal from '@/components/modals/RefreshObjectModal.vue'
import Page from '@/models/Page.js'
import PagePaths from '@/pages/PagePaths.vue'
import * as types from '@/store/mutation-types'

export default {
    name: 'PagesEdit',
    components: {
        PagePaths,
        RefreshObjectModal,
        BlockList,
    },

    beforeRouteLeave (from, to, next) {
        this.$vfm.hide('block-type-modal')
        this.$eventBus.$off('block-type-added', this.blockTypeAdded)
        this.$eventBus.$off('page-updated-echo', this.askToUpdatePage)
        this.$destroy()
        next()
    },

    data () {
        return {
            active: false,
            id: 0,
            queryParams: {
                include: 'template,template.groupedFields,slugs,localizedContent',
                locale: 'all',
                append: 'paths',
            },

            page: {
                id: 0,
                updated_at: '2020-01-01 10:00:00',
                localizedContent: {
                    sv: {},
                },

                template: {
                    data: {},
                },
            },

            paths: [],
            fields: {},
            content: {},
            groupedFields: [],
            repeaterKeys: ['boxes'],
            drag: false,
            localizedContent: {},
            showBlockTypeModalF: false,

        }
    },

    computed: {
        openCards() {
            return this.$store.getters['ui/openCards']
        },

        config () {
            return this.$store.getters['config/config']
        },

        locales () {
            return this.$store.getters['config/supportedLocales']
        },

        activeLocale: {
            get () {
                return this.$store.getters['config/activeLocale']
            },

            set (value) {
                this.$store.commit(`config/${types.SET_ACTIVE_LOCALE}`, value)
            },
        },

        currentUserIsFirstIn() {
            return this.$store.getters['echo/currentUserIsFirstIn']
        },

        devMode () {
            return this.$store.getters['config/devMode']
        },

        lockedBlocks() {
            if(this.devMode) {
                return false
            }

            return this.page.locked
        },
    },

    watch: {
        activeLocale() {
            this.checkBoxesArray()
        },
    },

    activated () {
        this.id = this.$route.params.id
        this.$eventBus.$on('block-type-added', this.blockTypeAdded)
        this.$eventBus.$on('page-updated-echo', this.askToUpdatePage)
        this.fetchPage()
        this.$nextTick(() => {
            if (this.$route.query.openComments) {
                this.$eventBus.$emit('open-comment-section')
            }
        })
    },

    methods: {
        askToUpdatePage(event) {
            this.$vfm.show('RefreshObjectModal', event)
        },

        openAllCards () {
            this.$eventBus.$emit('open-all-cards')
        },

        checkBoxesArray() {
            if (!this.localizedContent[this.activeLocale]) {
                return
            }

            if (!this.localizedContent[this.activeLocale].boxes) {
                this.$set(this.localizedContent[this.activeLocale], 'boxes', [])
            }
        },


        async updateContent () {
            try {
                const payload = {
                    name: this.page.name,
                    localizedContent: { ...this.localizedContent },
                }

                await Page.update(this.id, payload)
                this.$toast.success({ title: 'Utkastet har sparats' })
                this.$eventBus.$emit('page-updated')
            } catch (error) {
                console.error(error)
            }
        },

        async publishPage () {
            try {
                await this.updateContent(false)
                await Page.publish(this.id)
                this.$toast.success({ title: 'Sidan har publicerats!' })
            } catch (error) {
                console.log(error)
            }
        },

        async fetchPage () {
            const payload = {
                params: this.queryParams,
            }

            try {
                this.page.id = 0
                let localizedContent = null
                const { data } = await Page.show(this.id, payload)

                this.page = data
                this.fields = data.template.data.fields
                this.groupedFields = data.template.data.groupedFields.data
                localizedContent = { ...data.localizedContent.data }
                Object.keys(localizedContent).forEach((item) => {
                    this.$set(this.localizedContent, item, { ...localizedContent[item].content })
                })
                this.checkBoxesArray()
            } catch (error) {
                console.error(error)
            }
        },

        setLanguage (key) {
            this.activeLocale = key
            this.$eventBus.$emit('relayout-cards')
            this.$nextTick(() => {
                this.$eventBus.$emit('page-updated')
            })
        },

        async previewPage () {
            try {
                await this.updateContent()
                const data = await Page.signedPreview(this.id)
                let url = this.config.front_end_domain + data.computed_path + '?preview=' + data.encoded_signed_url

                if(Object.keys(this.locales).length > 1) {
                    url =  this.config.front_end_domain + '/' + this.activeLocale + data.computed_path + '?preview=' + data.encoded_signed_url
                }

                window.open(url, 'fabriq-previw')
            } catch (error) {
                console.error(error)
            }
        },
    },
}
</script>
