<template>
    <svg v-if="thin"
         aria-hidden="true"
         focusable="false"
         data-icon="xmark"
         role="img"
         xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 320 512"
    ><path d="M317.656 402.344C320.781 405.469 320.781 410.531 317.656 413.656C314.529 416.783 309.471 416.783 306.344 413.656L160 267.312L13.656 413.656C10.529 416.783 5.471 416.783 2.344 413.656C-0.781 410.531 -0.781 405.469 2.344 402.344L148.687 256L2.344 109.656C-0.781 106.531 -0.781 101.469 2.344 98.344S10.531 95.219 13.656 98.344L160 244.688L306.344 98.344C309.469 95.219 314.531 95.219 317.656 98.344S320.781 106.531 317.656 109.656L171.312 256L317.656 402.344Z"
           fill="currentColor"
    /></svg>
    <svg
        v-else
        aria-hidden="true"
        focusable="false"
        role="img"
        viewBox="0 0 384 512"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M347 411C341 418 331 418 325 411L192 279L59 411C53 418 43 418 37 411C30 405 30 395 37 389L169 256L37 123C30 117 30 107 37 101C43 94 53 94 59 101L192 233L325 101C331 94 341 94 347 101C354 107 354 117 347 123L215 256L347 389C354 395 354 405 347 411Z"
            fill="currentColor"
        />
    </svg>
</template>
<script>
export default {
    name: 'XMarkIcon',
    props: {
        thin: {
            type: Boolean,
            default: false
        }
    }
}
</script>
