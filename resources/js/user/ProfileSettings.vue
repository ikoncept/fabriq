<template>
    <div>
        <UiSectionHeader class="mb-4">
            Din information
            <template #subtitle>
                {{ localUser.name }}
            </template>
            <template #tools>
                <div class="space-x-4 whitespace-nowrap">
                    <FButton
                        class="px-6 py-2.5 leading-none fabriq-btn btn-link"
                        back-button="home.index"
                    >
                        Avbryt
                    </FButton>
                    <FButton class="px-6 py-2.5 leading-none fabriq-btn btn-royal"
                             :click="updateUser"
                    >
                        Spara
                    </FButton>
                </div>
            </template>
        </UiSectionHeader>
        <UiCard>
            <template #header>
                Användarinformation
            </template>
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-4">
                    <FInput v-model="localUser.name"
                            label="Namn"
                            rules="required|email"
                    />
                </div>
                <div class="col-span-4">
                    <FInput v-model="localUser.email"
                            label="E-postadress"
                            rules="required|email"
                    />
                </div>
                <div class="flex flex-col justify-center col-span-4">
                    <FLabel>
                        Ändra profilbild
                    </FLabel>

                    <div class="flex items-center">
                        <FUpload :ref="fileUploadRef"
                                 endpoint="/api/user/image"
                                 types="image/*"
                                 class="flex-col w-auto"
                                 upload-name="image"
                                 without-loader
                                 :max-items="1"
                                 @error="handleImageUploadError($event)"
                                 @added-file="imageUploadError = null"
                                 @upload-complete="userImageSaved()"
                        >
                            <button
                                class="px-6 leading-none py-2.5 text-sm font-semibold fabriq-btn btn-royal"
                                v-text="imageUrl ? 'Byt profilbild' : 'Välj profilbild'"
                            />
                        </FUpload>

                        <img v-if="imageUrl"
                             :src="imageUrl"
                             alt=""
                             class="inline-block ml-4 border rounded-lg h-9 w-9"
                        >

                        <button v-if="imageUrl"
                                class="mt-auto ml-2 text-xs leading-none fabriq-btn btn-link"
                                @click="deleteUserImage()"
                        >
                            Ta bort
                        </button>
                    </div>

                    <span v-if="imageUploadError"
                          class="font-sans text-xs text-red-500"
                    >
                        <span class="inline-flex items-center mt-2 leading-none">
                            <CircleExclamationIcon class="w-5 h-5 mr-2" />
                            {{ imageUploadError }}
                        </span>
                    </span>
                </div>
                <hr class="w-full h-px col-span-12">
                <h3 class="col-span-12 text-lg text-gray-500">
                    Ändra lösenord
                </h3>
                <div class="col-span-4">
                    <FInput v-model="passwordFields.password"
                            type="password"
                            label="Nytt lösenord"
                    />
                </div>
                <div class="col-span-4">
                    <FInput v-model="passwordFields.password_confirmation"
                            type="password"
                            label="Bekräfta nytt lösenord"
                    />
                </div>
                <div class="col-span-4">
                    <FInput v-model="passwordFields.current_password"
                            type="password"
                            label="Nuvarande lösenord"
                    />
                </div>
            </div>
            <!-- <pre>{{ localUser }}</pre> -->
        </UiCard>
    </div>
</template>
<script>
import AuthenticatedUser from '~/models/AuthenticatedUser'

export default {
    name: 'ProfileSettings',
    beforeRouteLeave (from, to, next) {
        this.$destroy()
        next()
    },

    data () {
        return {
            localUser: {
                image: {
                    data: {}
                }
            },
            passwordFields: {
                password: '',
                password_confirmation: '',
                current_password: ''
            },
            fileUploadRef: 'file-upload',
            imageUploadError: null
        }
    },
    computed: {
        user () {
            return this.$store.getters['user/user']
        },
        imageUrl () {
            return this.localUser.image.data.thumb_src
        }
    },
    activated () {
        this.fetchUser()
    },
    methods: {
        handleImageUploadError (event) {
            this.imageUploadError = event.errors.image[0]
        },
        async fetchUser () {
            try {
                const { data } = await AuthenticatedUser.index()
                this.localUser = data
                this.$store.commit('user/SET_USER', this.localUser)
            } catch (error) {
                console.error(error)
            }
        },
        async sendVerificationRequest () {
            try {
                await AuthenticatedUser.sendVerificationRequest()
                this.$toast.success({ title: 'E-postmeddelandet har skickats!', message: 'Följ instruktionerna i mailet' })
            } catch (error) {
                console.error(error)
            }
        },
        async updateUser () {
            try {
                await AuthenticatedUser.update({ ...this.localUser, ...this.passwordFields })
                this.fetchUser()
                this.$toast.success({ title: 'Ändringarna har sparats!' })
            } catch (error) {
                console.error(error)
            }
        },
        async deleteUserImage () {
            await AuthenticatedUser.deleteImage()
            this.fetchUser().then(() => {
                this.$toast.success({ title: 'Profilbild borttagen' })
            })
        },
        userImageSaved () {
            this.fetchUser().then(() => {
                this.$toast.success({ title: 'Profilbild uppdaterad' })
            })

            this.$refs[this.fileUploadRef].UploadDropzone.removeAllFiles()
        }
    }
}
</script>
