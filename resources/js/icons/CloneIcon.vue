<template>
    <svg
        v-if="thin"
        aria-hidden="true"
        focusable="false"
        data-prefix="fat"
        data-icon="clone"
        role="img"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 512 512"
    ><path
        fill="currentColor"
        d="M456 0h-240C185.1 0 160 25.12 160 56v240C160 326.9 185.1 352 216 352h240C486.9 352 512 326.9 512 296v-240C512 25.12 486.9 0 456 0zM496 296c0 22.06-17.94 40-40 40h-240c-22.06 0-40-17.94-40-40v-240c0-22.06 17.94-40 40-40h240c22.06 0 40 17.94 40 40V296zM344 384c-4.422 0-8 3.578-8 8v64c0 22.06-17.94 40-40 40h-240c-22.06 0-40-17.94-40-40v-240c0-22.06 17.94-40 40-40h64C124.4 176 128 172.4 128 168S124.4 160 120 160h-64C25.13 160 0 185.1 0 216v240C0 486.9 25.13 512 56 512h240c30.88 0 56-25.12 56-56v-64C352 387.6 348.4 384 344 384z"
    /></svg>
</template>

<script>
export default {
    name: 'CloneIcon',
    props: {
        thin: {
            type: Boolean,
            default: false
        }
    }
}
</script>
