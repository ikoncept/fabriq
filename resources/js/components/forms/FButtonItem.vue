<template>
    <div class="flex space-x-12">
        <FInput v-model="localButton.text"
                class="w-80"
                :disabled="disabled"
                label="Knapptext"
        />
        <FInput v-model="localButton.linkType"
                input-type="radio"
                class="col-span-4 xl:col-span-3"
                :disabled="disabled"
                name="linkType"
                :options="[{ label: 'Intern', value: 'internal' }, { label: 'Extern', value: 'external' }, { label: 'Fil', value: 'file'}]"
                label="Länktyp"
        />
        <FInput v-if="value.linkType === 'external'"
                v-model="localButton.url"
                class="flex-1"
                :disabled="disabled"
                label="Länk"
                placeholder="https://exempel.se eller /min-sida"
        />
        <FSelect
            v-if="value.linkType === 'internal'"
            v-model="localButton.page_id"
            class="flex-1"
            value-key="id"
            :reduce-fn="page => page.id"
            label="Sida"
            :options="localPages"
            option-label="name"
            :disabled="disabled"
            name="internal_page"
            @search-focus="checkForPages"
            @open="checkForPages"
        >
            <template #prefix="option">
                <div v-for="pageIndex in option.depth"
                     :key="pageIndex"
                >
                    <div class="mr-3" />
                </div>
                <!-- {{ option.page.name }} -->
            </template>
        </FSelect>
        <FFileInput v-if="value.linkType === 'file'"
                    v-model="localButton.file"
                    class="flex-1"
                    :disabled="disabled"
                    label="Fil"
        />
    </div>
</template>
<script>
import PageTree from '~/models/PageTree'
export default {
    name: 'FButtonItem',
    props: {
        pages: {
            type: Array,
            default: () => {
                return []
            }
        },
        value: {
            type: Object,
            default: () => {
                return {
                    text: '',
                    linkType: 'internal',
                    page_id: null,
                    file: {}
                }
            }
        },
        disabled: {
            type: Boolean,
            default: false
        }
    },
    data () {
        return {
            localButton: this.value,
            localPages: this.pages
        }
    },
    mounted () {
        if (this.pages.length === 0) {
            this.fetchPages()
        }
        setTimeout(() => {
            this.$emit('input', this.localButton)
        }, 10)
    },
    methods: {
        async fetchPages () {
            try {
                const payload = {
                    params: {
                        selectOptions: true
                    }
                }
                const { data } = await PageTree.index(payload)
                this.localPages = data
            } catch (error) {
                console.error(error)
            }
        },

        checkForPages () {
            // alert('hehe')
            this.fetchPages()
            console.log('checking for pages')
        },
        emitupdate () {
        }
    }
}
</script>
