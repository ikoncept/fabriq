<template>
    <svg
        v-if="thin"
        aria-hidden="true"
        focusable="false"
        data-prefix="fa-thin"
        data-icon="browsers"
        role="img"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 576 512"
    ><path
        d="M472 448C467.578 448 464 451.594 464 456C464 478.062 446.062 496 424 496H72C41.125 496 16 470.875 16 440V152C16 129.938 33.938 112 56 112C60.422 112 64 108.406 64 104S60.422 96 56 96C25.125 96 0 121.125 0 152V440C0 479.688 32.297 512 72 512H424C454.875 512 480 486.875 480 456C480 451.594 476.422 448 472 448ZM528 0H144C117.49 0 96 21.49 96 48V368C96 394.51 117.49 416 144 416H528C554.51 416 576 394.51 576 368V48C576 21.49 554.51 0 528 0ZM112 48C112 30.355 126.355 16 144 16H184V96H112V48ZM560 368C560 385.645 545.645 400 528 400H144C126.355 400 112 385.645 112 368V112H560V368ZM560 96H200V16H528C545.645 16 560 30.355 560 48V96Z"
        fill="currentColor"
    /></svg>
</template>
<script>
export default {
    name: 'BrowsersIcon',
    props: {
        thin: {
            type: Boolean,
            default: false
        }
    }
}
</script>
