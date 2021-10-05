<template>
    <div>
        <div class="mb-1 text-sm">
            Du blev omnämnd i en kommentar av <span class="font-semibold">{{ item.notifiable.data.user.data.name }}</span> <span class="text-xs font-bold">för {{ item.created_at | localTime(null, true) }}</span>
        </div>
        <blockquote class="py-1 pl-2 pr-4 mb-4 text-xs leading-loose whitespace-pre-wrap border-l-4 line-clamp-1"
                    v-html="item.notifiable.data.comment"
        />
        <div class="mb-1 text-xs">
            Sida: {{ item.notifiable.data.page.data.name }}
        </div>
    </div>
</template>
<script>
export default {
    name: 'PageComment',
    props: {
        cleared: {
            type: Boolean,
            default: false
        },
        item: {
            type: Object,
            default: () => {}
        }
    }
}
</script>
