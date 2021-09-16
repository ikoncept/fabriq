<template>
    <VueFinalModal
        :lock-scroll="false"
        :name="name"
        classes="flex items-start justify-center sm:mt-12 mt-4"
        content-class="relative flex flex-col p-6 mx-4 bg-white rounded-lg shadow modal-content sm:mx-0"
        esc-to-close
        focus-trap
        v-bind="$attrs"
        @opened="focusDismiss"
        v-on="$listeners"
    >
        <span class="py-0">
            <div class="flex items-center justify-between">
                <h2
                    class="text-xl font-semibold text-gray-500"
                    v-text="header"
                >Ta bort menypunkt</h2>
                <button class
                        @click="$emit('input', false)"
                >
                    <XMarkIcon
                        class="block w-6 h-6 -mt-2 -mr-2 text-gray-400 hover:text-gray-700"
                    />
                </button>
            </div>
        </span>
        <div class="w-screen max-w-sm overflow-visible">
            <div class="w-full">
                <div class="py-8 text-sm font-semibold"
                     v-text="text"
                />
                <div v-if="htmlText"
                     v-html="htmlText"
                />
                <slot />
            </div>
        </div>
        <div class="rounded-b-lg">
            <div class>
                <div class="flex justify-between">
                    <button
                        ref="dismissButton"
                        class="px-8 py-2.5 transition-all fabriq-btn btn-outline-royal"
                        @click="onDismissed"
                    >
                        {{ dismissText }}
                    </button>
                    <button
                        class="px-8 py-2.5 fabriq-btn btn-red"
                        @click="onConfirm"
                    >
                        {{ confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </VueFinalModal>
</template>
<script>
// import { setInteractionMode } from 'vee-validate'
export default {
    name: 'FConfirm',
    inheritAttrs: false,
    props: {
        name: {
            type: String,
            required: false,
            default: () => {
                return Math.random().toString(20).substr(2, 6)
            }
        },
        header: {
            type: String,
            default: 'Bekräfta ditt val',
            required: false
        },
        text: {
            type: String,
            default: '',
            required: false
        },
        htmlText: {
            type: String,
            default: '',
            required: false
        },
        dismissText: {
            type: String,
            default: 'Avbryt'
        },
        confirmText: {
            type: String,
            default: 'Bekräfta'
        },
        onConfirm: {
            type: Function,
            default: () => null
        }
    },
    methods: {
        focusDismiss () {
            this.$refs.dismissButton.focus()
        },
        onDismissed () {
            this.$vfm.hide(this.name)
        }
    }
}
</script>
