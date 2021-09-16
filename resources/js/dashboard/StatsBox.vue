<template>
    <UiStatsCard>
        <template #title>
            {{ title }}
        </template>
        <div class="text-4xl font-black text-center" />
        <template #value>
            <!-- eslint-disable-next-line -->
            <number
                ref="number1"
                :from="0"
                :to="number"
                :format="theFormat"
                :duration="1.4"
                :delay="delay"
                easing="Power1.easeOut"
            />
        </template>
    </UiStatsCard>
</template>
<script>
export default {
    name: 'StatsBox',
    props: {
        number: {
            type: Number,
            default: 0
        },
        delay: {
            type: Number,
            default: 1
        },
        title: {
            type: String,
            default: ''
        }
    },
    methods: {
        theFormat (number) {
            return number.toFixed(0)
        }
    }
}
</script>
