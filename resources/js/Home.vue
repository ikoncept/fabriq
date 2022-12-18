<template>
    <div class="mt-16">
        <div class="grid flex-grow grid-cols-4 gap-8">
            <StatsBox
                :delay="0"
                :number="pageCount"
                title="Antal sidor"
            />
            <StatsBox
                :delay="0"
                :number="articleCount"
                title="Antal nyheter"
            />
            <StatsBox
                :delay="0"
                :number="imageCount"
                title="Antal bilder"
            />
            <StatsBox
                :delay="0"
                :number="fileCount"
                title="Antal filer"
            />
        </div>
        <div class="absolute bottom-0 left-0 right-0 flex flex-col items-center mb-8 text-xs">
            Fabriq CMS är byggt av
            <a
                href="https://ikoncept.se"
                target="_blank"
                rel="noreferrer noopener"
            >
                <img
                    src="@/../fabriq/images/ikoncept-logo.svg"
                    alt="Ikoncept logotyp"
                    class="block h-12 mt-2"
                >
            </a>
        </div>
    </div>
</template>
<script>
import StatsBox from '@/dashboard/StatsBox.vue'
import Article from '@/models/Article.js'
import File from '@/models/File.js'
import Image from '@/models/Image.js'
import Page from '@/models/Page.js'
export default {
    name: 'HomeIndex',
    components: {
        StatsBox
    },
    data () {
        return {
            pageCount: 0,
            articleCount: 0,
            imageCount: 0,
            fileCount: 0
        }
    },
    activated () {
        this.fetchPageCount()
        this.fetchArticleCount()
        this.fetchFileCount()
        this.fetchImageCount()
    },
    methods: {
        async fetchPageCount () {
            const { data } = await Page.count()
            this.pageCount = data.count
        },
        async fetchArticleCount () {
            const { data } = await Article.count()
            this.articleCount = data.count
        },
        async fetchImageCount () {
            const { data } = await Image.count()
            this.imageCount = data.count
        },
        async fetchFileCount () {
            const { data } = await File.count()
            this.fileCount = data.count
        }
    }
}
</script>
