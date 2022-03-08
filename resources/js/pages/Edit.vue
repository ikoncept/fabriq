<template>
    <div>
        <UiSectionHeader>
            Redigera sida
            <template #subtitle>
                {{ page.name }}
            </template>
            <template #tools>
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
            </template>
        </UiSectionHeader>
        <FTabs v-if="Object.keys(locales).length"
               @change="setLanguage"
        >
            <FTab v-for="(locale, lIndex) in locales"
                  :key="lIndex"
                  :value-key="lIndex"
                  :title="locale.native"
            >
                <UiCard collapsible
                        group="pages-ettings"
                        sync-groups
                >
                    <template #header>
                        Sidinställningar
                    </template>
                    <div class="grid grid-cols-3 gap-x-6 gap-y-6">
                        <FInput v-model="page.name"
                                label="Namn"
                                name="name"
                        />
                        <FInput v-model="page.template.data.name"
                                label="Sidtyp"
                                name="template.data.name"
                                disabled
                        />
                        <FInput v-if="Object.keys(localizedContent).length > 0"
                                v-model="localizedContent[activeLocale].page_title"
                                name="*.page_title"
                                label="Sidtitel"
                                help-text="Visas i menyer"
                        />
                    </div>
                </UiCard>
                <div
                    class="grid grid-cols-4 space-x-6"
                >
                    <div class="col-span-4">
                        <div v-for="(fieldGroup, index) in groupedFields"
                             :key="'g' + index"
                        >
                            <UiCard v-if="! repeaterKeys.includes(index)"
                                    :group="index"
                                    sync-groups
                                    collapsible
                            >
                                <template #header>
                                    <h3 class>
                                        <span v-if="index == 'meta'">Meta-fält</span>
                                        <span v-else-if="index == 'main_content'">Sidhuvud</span>
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
                                        <div v-else-if="field.type == 'html'"
                                             class="mb-6"
                                        >
                                            <FEditor
                                                v-model="localizedContent[lIndex][field.key]"
                                                :label="field.name"
                                                :name="field.key"
                                            />
                                        </div>
                                        <div v-else-if="field.type == 'image'"
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
                                        <div v-else-if="field.type == 'video'"
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
                                        <div v-else-if="field.type == 'button'"
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
                                    </div>
                                </div>
                            </UiCard>
                            <div v-else>
                                <div class="flex items-center justify-between mt-12 mb-4">
                                    <h4 class="text-3xl font-light text-gray-700">
                                        Block
                                    </h4>
                                    <button class="flex items-center text-sm link"
                                            @click="showBlockTypeModal"
                                    >
                                        <PlusIcon class="w-5 h-5 mr-2" />Lägg till block
                                    </button>
                                </div>

                                <div v-if="! localizedContent[activeLocale].boxes">
                                    <div class="flex items-center justify-center h-48 border-2 border-dashed rounded border-royal-200">
                                        <div class="flex flex-col items-center">
                                            <div class="mb-4 text-xl font-light">
                                                Inga block har lagts till ännu
                                            </div>
                                            <button class="flex items-center text-sm link"
                                                    @click="showBlockTypeModal"
                                            >
                                                <PlusIcon class="w-5 h-5 mr-2" />Lägg till block
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div
                                        v-for="(field, repeaterIndex) in fieldGroup"
                                        :key="'f' + repeaterIndex + activeLocale"
                                        :class="[field.options ? field.options.classes : 'col-span-12']"
                                    >
                                        <Draggable v-model="localizedContent[activeLocale][field.key]"
                                                   handle=".handle"
                                                   tag="ul"
                                                   v-bind="dragOptions"
                                                   class="list-group"
                                                   @start="drag = true"
                                                   @end="drag = false"
                                        >
                                            <TransitionGroup type="transition"
                                                             :name="'flip-list-move'"
                                            >
                                                <li
                                                    v-for="(block, boxIndex) in localizedContent[activeLocale][field.key]"
                                                    :key="'alt' + boxIndex + activeLocale"
                                                    class="list-group-item"
                                                >
                                                    <UiCard v-if="block.id"
                                                            collapsible
                                                            :open-by-default="block.newlyAdded"
                                                    >
                                                        <template #header>
                                                            <div class="flex items-center justify-between">
                                                                <div class="flex items-center flex-1 space-x-6">
                                                                    <GripVerticalIcon class="block w-6 h-6 text-gray-300 cursor-move handle" />
                                                                    <div class="flex items-end space-x-6">
                                                                        <div class="leading-none">
                                                                            {{ block.name }}
                                                                        </div>
                                                                        <span class="inline-flex text-sm font-semibold leading-none text-gray-400">{{ block.block_type.name }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex items-center space-x-4">
                                                                    <!-- <ellipsis-icon class="w-6 h-6 mr-4" /> -->
                                                                    <button v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Klona block' }"
                                                                            class="focus:outline-none"
                                                                            @click.stop="cloneBlock(block)"
                                                                    >
                                                                        <CloneIcon
                                                                            thin
                                                                            class="h-6"
                                                                        />
                                                                    </button>
                                                                    <button v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Kopiera block-ID' }"
                                                                            v-clipboard="'#' + block.id"
                                                                            v-clipboard:success="copySuccess"
                                                                            class="focus:outline-none"
                                                                            type="button"
                                                                            @click.stop
                                                                    >
                                                                        <LinkIcon class="h-6"
                                                                                  thin
                                                                        />
                                                                    </button>
                                                                    <FButtonSwitch v-model="block.hidden"
                                                                                   class="self-center mb-1 "
                                                                    />
                                                                    <FConfirmDropdown confirm-question="Vill du ta bort detta blocket?"
                                                                                      class="relative w-6 h-6"
                                                                                      @confirmed="deleteBlock(boxIndex)"
                                                                    >
                                                                        <TrashIcon class="h-6 transition-colors duration-150 hover:text-red-500"
                                                                                   thin
                                                                        />
                                                                    </FConfirmDropdown>
                                                                    <div class="w-px h-8 mx-6 bg-gray-300" />
                                                                </div>
                                                            </div>
                                                        </template>
                                                        <KeepAlive>
                                                            <Component
                                                                :is="block.block_type.component_name"
                                                                :content.sync="localizedContent[activeLocale][field.key][boxIndex]"
                                                                :value="block"
                                                                :index="boxIndex"
                                                                @input="block = $event.target.value"
                                                                @repeater-updated="refreshBlock"
                                                            />
                                                        </KeepAlive>
                                                    </UiCard>
                                                </li>
                                            </TransitionGroup>
                                        </Draggable>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </FTab>
        </FTabs>
    </div>
</template>
<script>
import Page from '~/models/Page'
import Draggable from 'vuedraggable'
export default {
    name: 'PagesEdit',
    components: { Draggable },
    beforeRouteLeave (from, to, next) {
        this.$vfm.hide('block-type-modal')
        this.$eventBus.$off('block-type-added', this.blockTypeAdded)
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
                append: 'paths'
            },
            page: {
                id: 0,
                updated_at: '2020-01-01 10:00:00',
                localizedContent: {
                    sv: {}
                },
                template: {
                    data: {}
                }
            },
            fields: {},
            content: {},
            groupedFields: [],
            repeaterKeys: ['boxes'],
            drag: false,
            activeLocale: 'sv',
            localizedContent: {},
            showBlockTypeModalF: false
        }
    },
    computed: {
        config () {
            return this.$store.getters['config/config']
        },
        dragOptions () {
            return {
                animation: 200,
                group: 'description',
                disabled: false,
                ghostClass: 'ghost'
            }
        },
        locales () {
            return this.$store.getters['config/supportedLocales']
        }
    },
    activated () {
        this.id = this.$route.params.id
        this.$eventBus.$on('block-type-added', this.blockTypeAdded)
        this.fetchPage()
        this.$nextTick(() => {
            if (this.$route.query.openComments) {
                this.$eventBus.$emit('open-comment-section')
            }
        })
    },
    methods: {
        openAllCards () {
            this.$eventBus.$emit('open-all-cards')
        },
        async updateContent () {
            try {
                const payload = {
                    name: this.page.name,
                    localizedContent: { ...this.localizedContent }
                }
                await Page.update(this.id, payload)
                this.$toast.success({ title: 'Utkastet har sparats' })
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
                params: this.queryParams
            }
            try {
                this.page.id = 0
                const { data } = await Page.show(this.id, payload)
                this.page = data
                this.fields = data.template.data.fields
                this.groupedFields = data.template.data.groupedFields.data
                const localizedContent = { ...data.localizedContent.data }
                Object.keys(localizedContent).forEach((item) => {
                    this.$set(this.localizedContent, item, { ...localizedContent[item].content })
                })
            } catch (error) {
                console.error(error)
            }
        },
        setLanguage (key) {
            this.activeLocale = key
            this.$eventBus.$emit('relayout-cards')
        },
        showBlockTypeModal () {
            this.$vfm.show('block-type-modal')
        },
        blockTypeAdded (item) {
            if (!this.localizedContent[this.activeLocale].boxes) {
                this.$set(this.localizedContent[this.activeLocale], 'boxes', [])
            }
            this.localizedContent[this.activeLocale].boxes.push(JSON.parse(JSON.stringify(item)))
            this.$nextTick(() => {
                this.localizedContent[this.activeLocale].boxes[this.localizedContent[this.activeLocale].boxes.length - 1].newlyAdded = false
            })
        },
        deleteBlock (index) {
            this.localizedContent[this.activeLocale].boxes.splice(index, 1)
        },
        refreshBlock (payload) {
            this.localizedContent[this.activeLocale].boxes[payload.index] = { ...payload.item }
            console.log(payload.item)
        },
        copySuccess () {
            this.$toast.success({ title: 'Blockets ID har kopierats', message: 'Klista in som en extern länk i fältet till kontrollen du önskar länka blocket till.' })
        },
        async previewPage () {
            try {
                // this.$store.commit('config/SET_ACTIVE_LOCALE', 'sv')
                await this.updateContent()
                const data = await Page.signedPreview(this.id)
                const url = this.config.front_end_domain + data.computed_path + '?preview=' + data.encoded_signed_url
                window.open(url, 'fabriq-previw')
                console.log(data)
            } catch (error) {
                console.error(error)
            }
        },
        cloneBlock (block) {
            const clonedBlock = { ...block }
            clonedBlock.id = 'i' + Math.random().toString(20).substr(2, 6)
            clonedBlock.name = 'Kopia av ' + block.name
            this.blockTypeAdded({ ...clonedBlock })
        }
    }
}
</script>
<style>
.flip-list-move {
  transition: transform 0.5s;
}
.no-move {
  transition: transform 0s;
}
.ghost {
  opacity: 0.5;
  /* background: #c8ebfb; */
}
.list-group {
  /* min-height: 20px; */
}
.list-group-item {
  /* cursor: move; */
}
.flip-list-move {
    transition: transform 0.5s;
}
.no-move {
    transition: transform 0s;
}
.ghost,
.sortable-ghost {
    opacity: 0.5;
}
.list-group {
    /* min-height: 20px; */
}
.list-group-item {
    /* cursor: move; */
}
.list-group-item i {
    cursor: pointer;
}
</style>
