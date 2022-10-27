<template>
    <svg
        v-if="thin"
        aria-hidden="true"
        focusable="false"
        data-prefix="fa-thin"
        data-icon="check"
        role="img"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 512 512"
    ><path
        d="M192 392C189.953 392 187.906 391.219 186.344 389.656L34.344 237.656C31.219 234.531 31.219 229.469 34.344 226.344S42.531 223.219 45.656 226.344L192 372.688L466.344 98.344C469.469 95.219 474.531 95.219 477.656 98.344S480.781 106.531 477.656 109.656L197.656 389.656C196.094 391.219 194.047 392 192 392Z"
        fill="currentColor"
    /></svg>
    <svg
        v-else
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 448 512"
    ><!--! Font Awesome Pro 6.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path
        fill="currentColor"
        d="M443.3 100.7C449.6 106.9 449.6 117.1 443.3 123.3L171.3 395.3C165.1 401.6 154.9 401.6 148.7 395.3L4.686 251.3C-1.562 245.1-1.562 234.9 4.686 228.7C10.93 222.4 21.06 222.4 27.31 228.7L160 361.4L420.7 100.7C426.9 94.44 437.1 94.44 443.3 100.7H443.3z"
    /></svg>
</template>

<script>
export default {
    name: 'CheckIcon',
    props: {
        thin: {
            type: Boolean,
            default: false
        }
    }
}
</script>
