<template>
    <img
        v-if="!thumbnail"
        :id="image.id"
        ref="image"
        :alt="image.alt"
        :src="image.src"
        :srcset="image.srcset"
        :title="image.caption"
        sizes="1px"
    >
    <img v-else
         :id="image.id"
         :alt="image.alt"
         :src="imageSrc"
         :class="imageClasses"
         class="bg-checkered-sm"
    >
</template>
<script>

export default {
    name: 'UiImagePresenter',
    props: {
        image: {
            type: Object,
            required: false,
            default () {
                return {
                    srcset: '',
                    src: ''
                }
            }
        },
        imageClasses: {
            type: String,
            default: ''
        },
        thumbnail: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        imageSrc () {
            if (this.image.mime_type.includes('image/svg') && this.thumbnail) {
                return this.image.src
            }
            return this.image.thumb_src
        }
    },
    created () {
    },
    mounted () {
        if (this.thumbnail) {
            return
        }
        const image = this.$refs.image
        window.requestAnimationFrame(() => {
            if (!(image.size = image.getBoundingClientRect().width)) return
            image.sizes = Math.ceil(image.size / window.innerWidth * 100) + 'vw'
        })
    }
}
</script>
<style>
    .bg-checkered-sm {
        background-image: url('/dist/images/checkered.svg');
        background-size: 30px 23px;
        background-position: top top;
    }
    .bg-checkered-lg {
        background-image: url('/dist/images/checkered.svg');
        background-size: 4rem 3rem;
        background-position: top top;
    }
</style>
