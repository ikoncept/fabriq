<template>
    <form
        action="/logout"
        method="POST"
        @submit.prevent="logout"
    >
        <input v-model="csrfToken"
               name="_token"
               type="hidden"
        >
        <slot>
            <FButton
                title="Logga ut"
                spinner-color="text-royal-500"
                :click="logout"
                class="block w-full text-gray-700 outline-none"
            >
                <SignOutIcon class="w-6 " />
            </FButton>
        </slot>
    </form>
</template>
<script>
import Cookies from 'js-cookie'
import axios from 'axios'
export default {
    name: 'LogoutForm',
    data () {
        return {
            csrfToken: ''
        }
    },
    mounted () {
        // this.csrfToken = window.fabriqCms.csrfToken
    },
    methods: {
        async logout () {
            const payload = {
                headers: {
                    'X-XSRF-TOKEN': Cookies.get('XSRF-TOKEN')
                }
            }
            await axios.post('logout', payload)
            window.location.reload()
        }
    }
}
</script>
