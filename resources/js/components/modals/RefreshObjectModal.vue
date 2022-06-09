<template>
    <FModal
        v-model="showModal"
        :name="name"
        :width="width"
        :esc-to-close="false"
        :click-to-close="false"
        hide-close-button
        @opened="emitOpened"
        @before-open="emitBeforeOpen"
    >
        <template #title>
            <slot name="title" />
        </template>

        <template #default="{ params }">
            <slot :params="params" />
        </template>


        <template #actions="{ params }">
            <slot
                name="actions"
                :params="params"
            >
                <div class="flex justify-center space-x-4">
                    <div>
                        <FButton
                            :click="refreshPage"
                            class="px-8 py-2.5 leading-none fabriq-btn btn-royal"
                        >
                            Ladda om sidan
                        </FButton>
                    </div>
                </div>
            </slot>
        </template>
    </FModal>
</template>
<script>
export default {
    name: 'RefreshObjectModal',
    props: {
        show: {
            type: Boolean,
            default: false
        },
        name: {
            type: String,
            default: 'RefreshObjectModal',
        },
        width: {
            type: String,
            default: 'max-w-md'
        }
    },
    data () {
        return {
            showModal: false
        }
    },
    methods: {
        async refreshPage() {
            window.location.reload()
        },
        emitOpened () {
            this.$emit('opened')
        },
        emitBeforeOpen () {
            this.$emit('before-open')
        }
    }
}
</script>
