<template>
    <div>
        <FModal v-model="show"
                :name="name"
                width="max-w-xl"
        >
            <template #title>
                Lägg till bild från URL
            </template>
            <ValidationObserver v-slot="{ invalid }"
                                ref="observer"
            >
                <form @submit.prevent="addImage">
                    <FInput v-model="imageUrl"
                            name="url"
                            class="mb-6"
                            validation-mode="aggressive"
                            label="Webbadress"
                            placeholder="https://via.placeholder.com/140x100.png"
                            rules="required|url"
                    />
                    <div class="flex justify-end">
                        <FButton
                            :click="addImage"
                            :disabled="invalid"
                            class="py-2.5 px-6 fabriq-btn btn-royal"
                        >
                            Lägg till
                        </FButton>
                    </div>
                </form>
            </ValidationObserver>
        </FModal>
    </div>
</template>
<script>
import Image from '~/models/Image'
export default {
    name: 'AddImageFromUrlModal',
    props: {
        name: {
            type: String,
            required: true
        },
        width: {
            type: String,
            default: 'max-w-xl'
        }
    },
    data () {
        return {
            show: false,
            imageUrl: ''
        }
    },
    methods: {
        async addImage () {
            const result = await this.$refs.observer.validate()
            if (!result) {
                return
            }
            const payload = {
                url: this.imageUrl
            }
            await Image.store(payload)
            this.$emit('image-added')
            this.$toast.success({ title: 'Bilden har lagts till!' })
            this.$vfm.hide(this.name)
            setTimeout(() => {
                this.imageUrl = ''
                this.$nextTick(() => {
                    this.$refs.observer.reset()
                })
            }, 300)
        }
    }
}
</script>
