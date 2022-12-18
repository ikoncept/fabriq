<template>
    <img
        v-if="!thumbnail"
        :id="image.id"
        ref="image"
        :alt="image.alt"
        loading="lazy"
        :src="image.src"
        :srcset="image.srcset"
        :title="image.caption"
        :style="[customCrop]"
        :width="image.width"
        :height="image.height"
        sizes="1px"
    >
    <img
        v-else
        :id="image.id"
        :alt="image.alt"
        loading="lazy"
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
        },
        disableCustomCrop: {
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
        },
        customCrop () {
            if (!this.image.custom_crop || this.disableCustomCrop) {
                return ''
            }

            return {
                objectPosition: `${this.image.x_position} ${this.image.y_position}`
            }
        }
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
</style>
