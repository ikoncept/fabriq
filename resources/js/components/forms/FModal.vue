<template>
    <VueFinalModal
        :lock-scroll="false"
        classes="flex items-start justify-center sm:mt-12 mt-4"
        content-class="relative flex flex-col mx-4 bg-white rounded-lg shadow modal-content sm:mx-0"
        v-bind="$attrs"
        :click-to-close="clickToClose"
        :esc-to-close="escToClose"
        :name="name"
        @beforeOpen="event => event.ref.params"
        v-on="$listeners"
    >
        <template #default="{ params }">
            <span class="py-6 border-b">
                <div class="flex items-center justify-between px-6">
                    <span class="flex-1 text-xl font-light">
                        <slot
                            name="title"
                            :params="params"
                        />
                    </span>
                    <button
                        v-if="!hideCloseButton"
                        class
                        @click="$emit('input', false)"
                    >
                        <XMarkIcon
                            thin
                            class="block w-8 h-8 text-gray-800 "
                        />
                    </button>
                </div>
            </span>
            <div
                class="relative z-0 w-screen"
                :class="[width, overflow]"
            >
                <div class="w-full px-6 py-3">
                    <slot :params="params" />
                    <div class="my-4">
                        <slot
                            class="modal__action"
                            name="actions"
                        />
                    </div>
                </div>
            </div>
        </template>
    </VueFinalModal>
</template>
<script>
export default {
    name: 'FModal',
    inheritAttrs: false,
    props: {
        name: {
            type: String,
            required: true
        },
        width: {
            type: String,
            default: 'max-w-3xl'
        },
        overflow: {
            type: String,
            default: 'overflow-visible'
        },
        clickToClose: {
            type: Boolean,
            default: true
        },
        escToClose: {
            type: Boolean,
            default: true
        },
        hideCloseButton: {
            type: Boolean,
            default: false
        }
    }
}
</script>
<style scoped>
::v-deep .modal-content {
    max-height: 90% !important;
}
/* .vfm__content {
} */
</style>
