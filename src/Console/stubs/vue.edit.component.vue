<template>
    <div>
        <UiSectionHeader>
            Redigera {{ swedishName }}
            <template #subtitle>
                {{ item.name }}
            </template>
            <template #tools>
                <div class="space-x-4 whitespace-nowrap">
                    <FButton
                        class="px-6 py-2.5 leading-none fabriq-btn btn-link"
                        back-button="{{ pluralModelVariable }}.index"
                    >
                        Avbryt
                    </FButton>
                    <FButton class="px-6 py-2.5 leading-none fabriq-btn btn-royal"
                             :click="updateItem"
                    >
                        Spara
                    </FButton>
                </div>
            </template>
        </UiSectionHeader>
        <Transition name="fade">
            <UiCard v-show="item.id">
                <template #header>
                    Information
                </template>
                <div class="grid grid-cols-12 gap-x-6 gap-y-6">
                    <FInput v-model="item.name"
                            label="Namn"
                            class="col-span-3"
                            name="name"
                    />
                    <div class="col-span-12">
                        <FTabs>
                            <FTab v-for="(locale, key) in locales"
                                  :key="locale.regional"

                                  :title="locale.native"
                            >
                                <div v-if="localizedContent[key]"
                                     class="grid grid-cols-3 gap-x-6 gap-y-6"
                                >
                                    <!-- <div class="col-span-1">
                                        <FInput v-model="localizedContent[key].text"
                                                name="text"
                                                label="Text"
                                        />
                                    </div>
                                    <FEditor
                                        v-model="localizedContent[key].body"
                                        name="body"
                                        class="col-span-3"
                                        label="Innehåll"
                                    /> -->
                                </div>
                            </FTab>
                        </FTabs>
                    </div>
                </div>
            </UiCard>
        </Transition>
        <div v-show="! item.id">
            <div class="flex justify-center">
                <span class="inline-flex ml-4 animate-spin">
                    <SpinIcon class="w-10 h-10 text-royal-500" />
                </span>
            </div>
        </div>
    </div>
</template>
<script>
import {{ model }} from '~/models/{{ model }}'
export default {
    name: '{{ model }}sEdit',
    beforeRouteLeave (from, to, next) {
        setTimeout(() => {
            this.item.id = 0
        }, 300)
        next()
    },
    data () {
        return {
            id: 0,
            item: {
                name: ''
            },
            content: {},
            localizedContent: {}
        }
    },
    computed: {
        locales () {
            return this.$store.getters['config/supportedLocales']
        }
    },
    activated () {
        this.id = this.$route.params.id
        this.fetchItem()
    },
    methods: {
        mapLocalizedContent (data) {
            if (!data.localizedContent) {
                this.$set(this.localizedContent, 'sv', {})
                this.$set(this.localizedContent, 'en', {})
                return
            }
            const localizedContent = { ...data.localizedContent.data }
            Object.keys(localizedContent).forEach((item, key) => {
                this.$set(this.localizedContent, item, { ...localizedContent[item].content })
            })
        },
        async fetchItem () {
            try {
                const payload = {
                    params: {
                        include: 'localizedContent,content'
                    }
                }
                const { data } = await {{ model }}.show(this.id, payload)
                this.item = data
                if(data.content)  {
                    this.content = { ...data.content.data }
                }
                this.mapLocalizedContent(data)
            } catch (error) {
                console.error(error)
            }
        },
        async updateItem () {
            try {
                this.item.content = { ...this.content }
                this.item.localizedContent = { ...this.localizedContent }
                await {{ model }}.update(this.id, this.item)
                this.$toast.success({ title: 'Objektet har uppdaterats!' })
            } catch (error) {
                console.error(error)
            }
        }
    }
}
</script>
