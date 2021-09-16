<template>
    <div class="mt-8 info-placeholder">
        <div class="flex items-center justify-center border border-dashed rounded-md min-h-36 border-royal-300">
            <div class="text-center">
                <div class="text-xl font-light">
                    <slot name="header" />
                </div>
                <slot name="link" />
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'UiDashedBox'
}
</script>
