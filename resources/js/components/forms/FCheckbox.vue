<template>
    <div class="flex">
        <!-- wip -->
        <input
            :id="'chk' + _uid"
            :name="'chk' + _uid"
            type="checkbox"
            :disabled="disabled"
            :value="value"
            class="w-5 h-5 fabriq-checkbox form-checkbox focus:outline-none focus:ring-offset-3 focus:ring-1 focus:ring-royal-300"
            @change="$emit('input', $event.target.value)"
        >
        <label
            :for="'chk' + _uid"
            class="block ml-3 text-sm font-medium text-gray-700"
        >
            <slot />
        </label>
    </div>
</template>

<script>
export default {
    name: 'FCheckbox',
    props: {
        label: {
            type: String,
            default: ''
        },
        value: {
            type: [String, Array, Object, Boolean],
            default: ''
        },
        disabled: {
            type: Boolean,
            default: false
        }
    }
}
</script>

<style>
</style>
