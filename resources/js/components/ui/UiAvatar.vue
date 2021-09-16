<template>
    <img
        :src="`https://unavatar.now.sh/${email}?fallback=https://source.boringavatars.com/beam/120/${email}?colors=bd9b66,0b3b5b`"
        :alt="`Profilbild för ${email}`"
    >
</template>
<script lang="ts">
import Vue from 'vue'
export default Vue.extend({

})
</script>
<script>
export default {
    name: 'UiAvatar',
    props: {
        email: {
            type: String,
            default: '',
            required: true
        }
    }
}
</script>
