<template>
    <div class="grid grid-cols-12 gap-x-6 gap-y-6">
        <FInput
            v-model="localContent.name"
            name="name"
            class="col-span-12 md:col-span-4"
            label="Namn"
            rules="required"
            help-text="Visas endast internt"
        />
        <div class="col-span-8" />
        <FInput
            v-model="localContent.header"
            name="header"
            class="col-span-12 lg:col-span-5"
            label="Rubriktext"
        />
        <FInput
            v-model="localContent.subheader"
            class="col-span-12 lg:col-span-4"
            name="header"
            label="Underrubrik"
        />
        <FSelect
            v-model="localContent.columns"
            name="columns"
            class="col-span-12 lg:col-span-3"
            :reduce-fn="column => column"
            :options="[1, 2, 3, 4]"
            label="Antal kolumner"
        />
        <FEditor
            v-model="localContent.body"
            class="col-span-12"
            label="Text"
        />
        <div class="col-span-12 buttons">
            <FButtonList v-model="localContent.buttons">
                Knappar
            </FButtonList>
        </div>
    </div>
</template>
<script>
export default {
    name: 'TextBlock',
    props: {
        content: {
            type: [Array, Object],
            required: true,
            default: () => {
                return {
                    name: ''
                }
            }
        }
    },
    data () {
        return {
            localContent: this.content
        }
    }
}
</script>
