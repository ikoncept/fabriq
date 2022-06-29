<template>
    <div>
        <UiSectionHeader>
            Redigera nyhet
            <template #subtitle>
                {{ article.name }}
            </template>
            <template #tools>
                <div class="space-x-4 whitespace-nowrap">
                    <FButton
                        class="px-6 py-2.5 leading-none fabriq-btn btn-link"
                        back-button="articles.index"
                    >
                        Avbryt
                    </FButton>
                    <FButton
                        class="px-6 py-2.5 leading-none fabriq-btn btn-royal"
                        :click="updateArticle"
                    >
                        Spara
                    </FButton>
                </div>
            </template>
        </UiSectionHeader>
        <UiCard v-if="article.id">
            <template #header>
                <div class="flex justify-between">
                    <span>Generell information</span>
                    <PresenceInfo />
                </div>
            </template>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-6">
                <!-- <div class="col-span-3"> -->
                <FInput
                    v-model="article.name"
                    label="Namn"
                    class="col-span-1"
                    name="name"
                    rules="required"
                    placeholder="Namnet för nyheten"
                    help-text="Visas internt"
                />
                <div class="col-span-2" />
                <!-- </div> -->
                <FInput
                    v-model="content.title"
                    label="Titel"
                    name="title"
                    rules="required"
                    placeholder="Skriv titel här..."
                />
                <div class="flex flex-col space-y-4">
                    <FDatePicker
                        v-model="article.publishes_at"
                        label="Publiceringstid"
                        placeholder="Klicka för att välja tid"
                        mode="dateTime"
                        clearable
                        name="publishes_at"
                        class="mb-3"
                    >
                        <template #clear>
                            Rensa
                        </template>
                    </FDatePicker>
                    <div>
                        <FLabel name="has_unpublished_time">
                            Avpublicera vid tidpunkt
                        </FLabel>
                        <div class="flex items-center mb-6">
                            <FSwitch
                                v-model="article.has_unpublished_time"
                                name="has_unpublished_time"
                            />
                            <div
                                class="ml-2 text-sm"
                                v-text="article.has_unpublished_time ? 'Ja' : 'Nej'"
                            />
                        </div>
                        <div v-if="article.has_unpublished_time">
                            <FDatePicker
                                v-model="article.unpublishes_at"
                                label="Avpubliceringstid"
                                name="unpublishes_at"
                                placeholder="Klicka för att välja tid"
                                mode="dateTime"
                                clearable
                                class="mb-3"
                            >
                                <template #clear>
                                    Rensa
                                </template>
                            </FDatePicker>
                        </div>
                    </div>
                </div>
                <FImageInput
                    v-if="article.id"
                    v-model="content.image"
                    label="Bild"
                    name="image"
                    class="row-span-2 xl:col-span-1 col-span2 max-w-96"
                    :model-id="article.id"
                />
                <FInput
                    v-model="content.preamble"
                    label="Ingress"
                    name="preamble"
                    textarea
                    class="col-span-2"
                    placeholder="Skriv en ingress här..."
                />
                <FEditor
                    v-model="content.body"
                    class="col-span-2 mb-4"
                    name="body"
                />
            </div>
        </UiCard>
    </div>
</template>
<script>
import Article from '@/models/Article.js'
export default {
    name: 'ArticlesEdit',
    data () {
        return {
            id: 0,
            article: {
                id: 0,
                name: '',
                publishes_at: ''
            },
            content: {
                publishes_at: '',
                image: { id: 0 }
            },
            queryParams: {
                include: 'content,template,template.groupedFields'
            },
            usersIdle: []
        }
    },
    activated () {
        this.id = this.$route.params.id
        this.fetchArticle()
    },
    methods: {
        async fetchArticle () {
            try {
                const payload = {
                    params: this.queryParams
                }
                const { data } = await Article.show(this.id, payload)
                this.article = data
                this.content = { ...data.content.data }
            } catch (error) {
                console.error(error)
            }
        },
        async updateArticle () {
            try {
                this.article.content = { ...this.content }
                await Article.update(this.id, { ...this.article })
                this.$toast.success({ title: 'Nyheten har uppdaterats' })
            } catch (error) {
                console.error(error)
            }
        }
    }
}
</script>
