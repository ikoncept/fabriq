<template>
    <form
        action="/logout"
        method="POST"
    >
        <input v-model="csrfToken"
               name="_token"
               type="hidden"
        >
        <slot>
            <button
                title="Logga ut"
                class="block w-full text-gray-700 outline-none"
            >
                <SignOutIcon class="w-6 " />
            </button>
        </slot>
    </form>
</template>
<script>

export default {
    name: 'LogoutForm',
    data () {
        return {
            csrfToken: ''
        }
    },
    mounted () {
        this.csrfToken = window.fabriqCms.csrfToken
    }
}
</script>
