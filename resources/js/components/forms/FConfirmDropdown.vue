<template>
    <UiDropdown ref="dropdown"
                :alignment="alignment"
                :margin-classes="alignment === 'top-right' ? '-mr-4 mt-2.5' : '-ml-4 mt-2.5'"
                :disabled="disabled"
                @open="focusDismiss"
    >
        <template #dropdown>
            <div class="text-white rounded bg-royal-500">
                <svg
                    class="absolute w-5 h-5 -mt-4 transform rotate-180 text-royal-500"
                    :class="alignment === 'top-right' ? 'mr-4 right-0 ' : 'left-0 ml-4'"
                    viewBox="0 0 100 100"
                >
                    <polygon points="0,0 100,0 50,60"
                             fill="currentColor"
                    />
                </svg>
                <div class="px-8 py-4 text-xs font-semibold whitespace-nowrap"
                     v-text="confirmQuestion"
                />
                <div class="flex text-sm">
                    <button ref="dismiss"
                            class="flex-1 font-bold bg-royal-500 transition-colors duration-150 py-2.5 hover:bg-royal-600 focus:outline-none rounded-bl"
                            type="button"
                            @click.stop="dismiss"
                            v-text="dismissText"
                    />
                    <button class="flex-1 font-bold bg-royal-500  py-2.5  hover:bg-royal-600  focus:outline-none rounded-br"
                            type="button"
                            @click.stop="confirm"
                            v-text="confirmText"
                    />
                </div>
            </div>
        </template>
        <slot />
    </UiDropdown>
</template>
<script>
export default {
    name: 'FConfirmDropdown',
    props: {
        dismissText: {
            type: String,
            default: 'Nej'
        },
        confirmText: {
            type: String,
            default: 'Ja'
        },
        confirmQuestion: {
            type: String,
            default: 'Vill du ta bort?'
        },
        alignment: {
            type: String,
            default: 'top-right'
        },
        disabled: {
            type: Boolean,
            default: false
        }
    },
    methods: {
        focusDismiss () {
            setTimeout(() => {
                this.$refs.dismiss.focus()
            }, 150)
        },
        dismiss () {
            this.$refs.dropdown.close()
            this.$emit('dismissed')
        },
        confirm () {
            this.$refs.dropdown.close()
            this.$emit('confirmed')
        }
    }
}
</script>
