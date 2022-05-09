<template>
    <svg
        v-if="thin"
        aria-hidden="true"
        focusable="false"
        data-prefix="fat"
        data-icon="file"
        role="img"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 384 512"
    ><path
        fill="currentColor"
        d="M374.6 150.6l-141.3-141.3c-6-6.002-14.14-9.373-22.62-9.373L64-.0001c-35.35 0-64 28.65-64 64l.0065 384c0 35.35 28.65 64 64 64H320c35.35 0 64-28.65 64-64V173.3C384 164.8 380.6 156.6 374.6 150.6zM361.4 159.1H248c-13.22 0-24-10.77-24-24V22.63L361.4 159.1zM320 495.1H64c-26.47 0-48-21.53-48-48v-384c0-26.47 21.53-48 48-48h144v119.1c0 22.06 17.94 40 40 40h120v272C368 474.5 346.5 495.1 320 495.1z"
    /></svg>
    <svg
        v-else
        aria-hidden="true"
        focusable="false"
        data-prefix="fal"
        data-icon="file"
        class="svg-inline--fa fa-file"
        role="img"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 384 512"
    ><path
        fill="currentColor"
        d="M365.3 125.3l-106.5-106.5C246.7 6.742 230.5 0 213.5 0L64-.0001c-35.35 0-64 28.65-64 64l.0065 384c0 35.35 28.65 64 64 64H320c35.35 0 64-28.65 64-64v-277.5C384 153.5 377.3 137.3 365.3 125.3zM342.6 147.9C346.1 151.3 348.4 155.5 349.9 160H240C231.2 160 224 152.8 224 144V34.08c4.477 1.566 8.666 3.846 12.12 7.299L342.6 147.9zM352 448c0 17.64-14.36 32-32 32H64c-17.64 0-32-14.36-32-32V64c0-17.64 14.36-32 32-32h128v112C192 170.5 213.5 192 240 192H352V448z"
    /></svg>
</template>

<script>
export default {
    name: 'FileIcon',
    props: {
        thin: {
            type: Boolean,
            default: false
        }
    }
}
</script>
