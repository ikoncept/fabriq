<template>
    <div class="px-4 py-5 overflow-hidden bg-white rounded-lg shadow sm:p-6">
        <dt class="text-sm font-medium text-gray-500 truncate">
            <slot name="title" />
        </dt>
        <dd class="mt-1 text-3xl font-semibold text-gray-900">
            <slot name="value" />
        </dd>
    </div>
</template>
<script>
export default {
    name: 'UiStatsCard'
}
</script>
