<template>
    <VueFinalModal
        :lock-scroll="false"
        classes="flex items-start justify-center sm:mt-12 mt-4"
        content-class="relative flex flex-col mx-4 bg-white rounded-lg shadow modal-content sm:mx-0"
        v-bind="$attrs"
        :click-to-close="clickToClose"
        esc-to-close
        :name="name"
        v-on="$listeners"
    >
        <span class="py-6 border-b">
            <div class="flex items-center justify-between px-6">
                <span class="flex-1 text-xl font-light">
                    <slot name="title" />
                </span>
                <button class
                        @click="$emit('input', false)"
                >
                    <XMarkIcon thin
                               class="block w-8 h-8 text-gray-800 "
                    />
                </button>
            </div>
        </span>
        <div class="relative z-0 w-screen"
             :class="[width, overflow]"
        >
            <div class="w-full px-6 py-3">
                <slot />
                <div class="my-4">
                    <slot class="modal__action"
                          name="actions"
                    />
                </div>
            </div>
        </div>
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
