<template>
    <svg
        v-if="thin"
        aria-hidden="true"
        focusable="false"
        data-prefix="fa-thin"
        data-icon="arrow-down-to-line"
        role="img"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 384 512"
    ><path
        d="M186.344 381.656C189.469 384.781 194.531 384.781 197.656 381.656L349.656 229.656C351.219 228.094 352 226.062 352 224S351.219 219.906 349.656 218.344C346.531 215.219 341.469 215.219 338.344 218.344L200 356.688V40C200 35.594 196.422 32 192 32S184 35.594 184 40V356.688L45.656 218.344C42.531 215.219 37.469 215.219 34.344 218.344S31.219 226.531 34.344 229.656L186.344 381.656ZM8 480H376C380.418 480 384 476.418 384 472S380.418 464 376 464H8C3.582 464 0 467.582 0 472S3.582 480 8 480Z"
        fill="currentColor"
    /></svg>
</template>

<script>
export default {
    name: 'ArrowDownLineIcon',
    props: {
        thin: {
            type: Boolean,
            default: false
        }
    }
}
</script>
