<template>
    <FModal v-model="show"
            name="menu-item-modal"
            width="max-w-3xl"
            :click-to-close="false"
            @closed="resetModal"
            @before-open="initModal"
    >
        <template #title>
            <span
                class="text-gray-700"
                v-text="isCreating ? 'Skapa menypunkt' : 'Redigera menypunkt'"
            />
        </template>
        <template #actions>
            <div class="flex justify-end space-x-4">
                <button
                    class="px-8 py-2.5 leading-none fabriq-btn btn-link"
                    @click="show = false"
                >
                    Stäng
                </button>
                <FButton
                    v-if="! isCreating"
                    :click="updateMenuItem"
                    class="px-8 py-2.5 leading-none fabriq-btn btn-royal"
                >
                    Spara
                </FButton>
                <FButton
                    v-else
                    :click="createMenuItem"
                    class="px-8 py-2.5 leading-none fabriq-btn btn-royal"
                >
                    Skapa menypunkt
                </FButton>
            </div>
        </template>
        <div
            class="relative py-2"
        >
            <div class="grid grid-cols-1 gap-x-6 sm:grid-cols-2">
                <fieldset class="mb-6">
                    <FInput v-model="mItem.type"
                            input-type="radio"
                            name="link_types"
                            label="Länktyp"
                            :options="[{ label: 'Intern', value: 'internal' }, { label: 'Extern', value: 'external' }]"
                    />
                </fieldset>
            </div>
            <div v-show="mItem.type === 'internal'"
                 class="mt-4 mb-6"
            >
                <ValidationObserver ref="internalObserver">
                    <FSelect
                        v-model="mItem.page_id"
                        value-key="id"
                        :reduce-fn="page => page.id"
                        label="Länkas till sidan"
                        :options="pages"
                        option-label="name"
                        rules="required"
                        name="pageSelect"
                    >
                        <template #prefix="option">
                            <div v-for="index in option.depth"
                                 :key="index"
                            >
                                <div class="mr-3" />
                            </div>
                        </template>
                    </FSelect>
                </ValidationObserver>
            </div>
            <div>
                <ValidationObserver
                    ref="externalObserver"
                >
                    <div class="grid grid-cols-12 gap-6">
                        <div v-if="mItem.type === 'external'"
                             class="grid grid-cols-2 col-span-12 gap-6"
                        >
                            <FInput
                                v-model="content.title"
                                class="mb-6"
                                help-text="Denna text visas i menyer"
                                label="Titel"
                                name="title"
                                :rules="!isCreating ? 'required' : ''"
                            />
                            <FInput
                                v-model="content.external_url"
                                label="URL"
                                placeholder="https://exempel.se"
                                name="url"
                                :rules="!isCreating ? 'required' : ''"
                                type="url"
                            />
                        </div>
                    </div>
                    <FEditor
                        v-if="ready"
                        :key="'html' + mItem.Id"
                        v-model="content.body"
                        label="Menytext"
                    />
                </ValidationObserver>
            </div>
        </div>
    </FModal>
</template>
<script>
import MenuItem from '~/models/MenuItem'
import PageTree from '~/models/PageTree'
// import OperatingHour from '~/models/OperatingHour'
function defaultCreationObject () {
    return {
        id: 0,
        type: 'internal',
        content: {
            data: {
                title: ''
            }
        }
    }
}
function defaultContentObject () {
    return {
        sv: {
            title: '',
            external_url: '',
            body: ''
        },
        en: {
            title: '',
            external_url: '',
            body: ''
        }
    }
}
export default {
    name: 'MenuItemModal',
    props: {
        isCreating: {
            type: Boolean,
            default: false
        }
    },
    data () {
        return {
            show: false,
            pages: [],
            localizedContent: {},
            content: {},
            ready: false,
            selectedOperatingHour: null,
            // operatingHours: [],
            mItem: {
                id: 0,
                type: 'internal',
                content: {
                    data: {
                        title: ''
                    }
                }
            }
        }
    },
    methods: {
        async initModal (parameters) {
            if (this.isCreating) {
                this.fetchPages()
                this.localizedContent = { ...defaultContentObject() }
                this.content = {
                    title: null,
                    url: null,
                    body: null
                }
            }

            if (!this.isCreating) {
                const id = parameters.ref.params
                this.mItem.id = id
                const promises = [this.fetchPages(), this.fetchMenuItem(), this.fetchOperatingHours()]
                await Promise.all(promises)
            }
        },
        async fetchPages () {
            try {
                const payload = {
                    params: {
                        // field: 'id,name',
                        selectOptions: true
                    }
                }
                const { data } = await PageTree.index(payload)
                this.pages = data
            } catch (error) {
                console.error(error)
            }
        },
        async fetchOperatingHours () {
            // try {
            //     const { data } = await OperatingHour.index()
            //     this.operatingHours = data
            // } catch (error) {
            //     console.error(error)
            // }
        },
        async createMenuItem () {
            try {
                let isValid = false
                if (!this.mItem.type) {
                    return
                }
                if (this.mItem.type === 'internal') {
                    isValid = await this.$refs.internalObserver.validate()
                    this.$refs.externalObserver.reset()
                } else if (this.mItem.type === 'external') {
                    isValid = await this.$refs.externalObserver.validate()
                    this.$refs.internalObserver.reset()
                }
                if (!isValid) {
                    return
                }
                const payload = {
                    item: this.mItem,
                    content: this.content
                }
                await MenuItem.store(this.$route.params.id, payload)
                this.$toast.success({ title: 'Menypunkten har skapats!' })
                this.$emit('created')
                this.show = false
            } catch (error) {
                console.error(error)
            }
        },
        async updateMenuItem () {
            let isValid = false

            if (this.mItem.type === 'internal') {
                isValid = await this.$refs.internalObserver.validate()
                this.$refs.externalObserver.reset()
            } else if (this.mItem.type === 'external') {
                isValid = await this.$refs.externalObserver.validate()
                this.$refs.internalObserver.reset()
            }
            if (!isValid) {
                this.$toast.warning({ title: 'Oj då!', message: 'Något är tosigt med datan du försöker skicka.' })
                return
            }
            try {
                // this.mItem.operating_hour = this.selectedOperatingHour
                const payload = {
                    item: this.mItem,
                    content: this.content
                }
                await MenuItem.update(this.mItem.id, payload)
                this.$emit('updated')
                this.show = false
            } catch (error) {
                console.error(error)
            }
        },
        async fetchMenuItem () {
            try {
                const payload = {
                    params: {
                        include: 'localizedContent,content'
                    }
                }
                const { data } = await MenuItem.show(this.mItem.id, payload)
                this.mItem = { ...data }
                this.content = data.content.data
                this.selectedOperatingHour = this.mItem.content.data.operating_hour?.id ?? null
                const localizedContent = { ...data.localizedContent.data }
                Object.keys(localizedContent).forEach((item, key) => {
                    this.$set(this.localizedContent, item, { ...localizedContent[item].content })
                })
                this.ready = true
            } catch (error) {
                console.error(error)
            }
        },
        resetModal () {
            this.ready = false
            this.mItem = { ...defaultCreationObject() }
            this.localizedContent = { ...defaultContentObject() }
            this.selectedOperatingHour = null
            this.$nextTick(() => {
                this.$refs.internalObserver.reset()
                this.$refs.externalObserver.reset()
            })
        }
    }
}
</script>
