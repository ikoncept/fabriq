<template>
    <div class="mt-4 info-placeholder">
        <div
            class="flex items-center justify-center py-3 border border-dashed rounded-md border-royal-300"
            :class="size"
        >
            <div class="text-center">
                <div class="mb-2 text-xl font-light">
                    <slot name="header" />
                </div>
                <slot name="link" />
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'UiDashedBox',
    props: {
        size: {
            type: String,
            default: 'min-h-[150px]'
        }
    }
}
</script>
