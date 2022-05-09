<template>
    <FModal
        v-model="showModal"
        :name="name"
        :width="width"
        @closed="resetCreateModal"
        @opened="emitOpened"
        @before-open="emitBeforeOpen"
    >
        <template #title>
            <slot name="title" />
        </template>
        <template #actions>
            <div class="flex justify-end space-x-4">
                <div class="flex space-x-4">
                    <button
                        class="px-8 py-2.5 leading-none fabriq-btn btn-link"
                        @click="showModal = false"
                    >
                        Avbryt
                    </button>
                    <FButton
                        :click="validateForm"
                        class="px-8 py-2.5 leading-none fabriq-btn btn-royal"
                    >
                        Lägg till
                    </FButton>
                </div>
            </div>
        </template>

        <ValidationObserver ref="observer">
            <form
                class="flex flex-col col-span-4 gap-y-4"
                @submit.prevent="validateForm"
            >
                <slot />
            </form>
        </ValidationObserver>
    </FModal>
</template>
<script>
export default {
    name: 'CreateModal',
    props: {
        show: {
            type: Boolean,
            default: false
        },
        name: {
            type: String,
            required: true
        },
        width: {
            type: String,
            default: 'max-w-xl'
        }
    },
    data () {
        return {
            showModal: false
        }
    },
    methods: {
        resetCreateModal () {
            this.$emit('closed')
            setTimeout(() => {
                this.$refs.observer.reset()
            }, 200)
        },
        async validateForm () {
            const result = await this.$refs.observer.validate()
            if (result) {
                this.$emit('validated')
            }
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
