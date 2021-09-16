<template>
    <div>
        <div class="text-center">
            <span class="font-display">
                <div class="mb-8 font-black tracking-wider md:text-xl">{{ message }}</div>
            </span>
            <RouterLink :to="{name:'home.index'}"
                        class="px-6 py-3 mt-8 font-sans text-xl leading-none shadow hover:shadow-lg fabriq-btn btn-gold"
            >
                Gå hem
            </RouterLink>
            <div />
        </div>
    </div>
</template>
<script>
export default {
    name: 'Errror404',
    data () {
        return {
            message: 'Sidan kunde inte hittas...tyvärr 🤦‍♀️'
        }
    }
}
</script>
