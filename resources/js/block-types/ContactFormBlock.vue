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
        <hr class="col-span-12">
        <FInput
            v-model="localContent.header"
            name="header"
            rules="required"
            class="col-span-12 lg:col-span-5"
            label="Rubriktext"
        />
        <FInput
            v-model="localContent.email"
            name="email"
            rules="required|email"
            class="col-span-12 lg:col-span-5"
            label="E-postadress"
        />
        <FEditor
            v-model="localContent.body"
            class="col-span-12 lg:col-span-10"
            label="Text"
        />
        <div class="col-span-12">
            <UiDashedBox>
                <template #header>
                    Autogenereat kontaktformulär
                </template>
            </UiDashedBox>
        </div>
    </div>
</template>
<script>
export default {
    name: 'ContactFormBlock',
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
