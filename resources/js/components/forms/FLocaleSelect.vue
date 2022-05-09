<template>
    <div>
        <select
            id="locales"
            v-model="activeLocale"
            class="w-full fabriq-input"
            name="locales"
            @change="$emit('change', activeLocale)"
        >
            <option
                v-for="(locale, key) in locales"
                :key="key"
                :value="key"
            >
                {{ locale.native }}
            </option>
        </select>
    </div>
</template>
<script>
import * as types from '~/store/mutation-types'
export default {
    name: 'FLocaleSelect',
    computed: {
        locales () {
            return this.$store.getters['config/supportedLocales']
        },
        activeLocale: {
            get () {
                return this.$store.getters['config/activeLocale']
            },
            set (value) {
                this.$store.commit(`config/${types.SET_ACTIVE_LOCALE}`, value)
            }
        }
    }
}
</script>
