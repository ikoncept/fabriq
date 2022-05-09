<template>
    <div>
        <UiSectionHeader>
            Redigera kontakt
            <template #subtitle>
                {{ contact.name }}
            </template>
            <template #tools>
                <div class="space-x-4 whitespace-nowrap">
                    <FButton
                        class="px-6 py-2.5 leading-none fabriq-btn btn-link"
                        back-button="contacts.index"
                    >
                        Avbryt
                    </FButton>
                    <FButton
                        class="px-6 py-2.5 leading-none fabriq-btn btn-royal"
                        :click="updateContact"
                    >
                        Spara
                    </FButton>
                </div>
            </template>
        </UiSectionHeader>
        <Transition name="fade">
            <UiCard v-show="contact.id">
                <template #header>
                    Information
                </template>
                <div class="grid grid-cols-12 gap-x-6 gap-y-6">
                    <FInput
                        v-model="contact.name"
                        label="Namn"
                        class="col-span-3"
                        name="name"
                    />
                    <div class="col-span-6">
                        <div class="flex space-x-6">
                            <FSelect
                                v-model="tags"
                                multiple
                                taggable
                                label="Kontaktgrupp"
                                class="w-96"
                                name="tags"
                                :reduce-fn="tag => tag"
                                :create-option="tag => ({ name: tag, value: null, type: 'contacts'})"
                                value-key="name"
                                option-label="name"
                                :push-tags="false"
                                :options="contactTags"
                            />
                            <FSwitch
                                v-model="contact.published"
                                column-layout
                            >
                                Visa
                            </FSwitch>
                        </div>
                    </div>
                    <FInput
                        v-model="contact.email"
                        label="E-post"
                        type="email"
                        class="col-span-4 row-start-2 "
                        name="email"
                    />
                    <FInput
                        v-model="contact.phone"
                        label="Telefonnummer"
                        class="col-span-2 row-start-2"
                        type="tel"
                        name="phone"
                    />
                    <FInput
                        v-model="contact.mobile"
                        label="Mobil"
                        class="col-span-2 row-start-2"
                        type="tel"
                        name="mobile"
                    />
                    <FInput
                        v-model="contact.sortindex"
                        type="number"
                        class="col-span-4 row-start-2"
                        label="Sorteringsindex"
                        help-text="Sorterar kontakter i stigande ordning (lägst först)"
                    />

                    <div class="col-span-12">
                        <div class="w-96">
                            <FImageInput
                                v-if="contact.id"
                                v-model="content.image"
                                name="image"
                                label="Kontaktbild"
                            />
                        </div>
                    </div>
                    <div class="col-span-12">
                        <FTabs>
                            <FTab
                                v-for="(locale, key) in locales"
                                :key="locale.regional"

                                :title="locale.native"
                            >
                                <div
                                    v-if="localizedContent[key]"
                                    class="grid grid-cols-3 gap-x-6 gap-y-6"
                                >
                                    <div class="col-span-1">
                                        <FInput
                                            v-model="localizedContent[key].position"
                                            name="position"
                                            label="Roll"
                                        />
                                    </div>
                                    <FEditor
                                        v-model="localizedContent[key].body"
                                        name="body"
                                        class="col-span-3"
                                        label="Text"
                                    />
                                </div>
                            </FTab>
                        </FTabs>
                    </div>
                </div>
            </UiCard>
        </Transition>
        <div v-show="! contact.id">
            <div class="flex justify-center">
                <span class="inline-flex ml-4 animate-spin">
                    <SpinIcon class="w-10 h-10 text-royal-500" />
                </span>
            </div>
        </div>
    </div>
</template>
<script>
import Tag from '~/models/Tag'
import Contact from '~/models/Contact'
export default {
    name: 'ContactsEdit',
    beforeRouteLeave (from, to, next) {
        setTimeout(() => {
            this.contact.id = 0
        }, 300)
        next()
    },
    data () {
        return {
            id: 0,
            contact: {
                name: '',
                hidden: false
            },
            tags: [],
            contactTags: [],
            content: {},
            localizedContent: {}
        }
    },
    computed: {
        locales () {
            return this.$store.getters['config/supportedLocales']
        },
        nameTags () {
            return this.tags.map(item => {
                return item.name
            })
        }
    },
    activated () {
        this.id = this.$route.params.id
        this.fetchContact()
        this.fetchTags()
    },
    methods: {
        mapLocalizedContent (data) {
            const localizedContent = { ...data.localizedContent.data }
            Object.keys(localizedContent).forEach((item, key) => {
                this.$set(this.localizedContent, item, { ...localizedContent[item].content })
            })
        },
        async fetchContact () {
            try {
                const payload = {
                    params: {
                        include: 'localizedContent,content,tags'
                    }
                }
                const { data } = await Contact.show(this.id, payload)
                this.contact = data
                this.content = { ...data.content.data }
                this.tags = [...data.tags.data]
                this.mapLocalizedContent(data)
            } catch (error) {
                console.error(error)
            }
        },
        async updateContact () {
            try {
                this.contact.tags = { ...this.nameTags }
                this.contact.content = { ...this.content }
                this.contact.localizedContent = { ...this.localizedContent }
                await Contact.update(this.id, this.contact)
                this.$toast.success({ title: 'Kontakten har uppdaterats!' })
            } catch (error) {
                console.error(error)
            }
        },
        async fetchTags () {
            try {
                const payload = {
                    params: {
                        'filter[type]': 'contacts'
                    }
                }
                const { data } = await Tag.index(payload)
                this.contactTags = data
            } catch (error) {
                console.error(error)
            }
        }
    }
}
</script>
