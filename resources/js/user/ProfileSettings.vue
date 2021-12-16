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
                <div class="col-span-4 flex flex-col justify-center">

                    <FLabel>
                        Ändra profilbild
                    </FLabel>

                    <div class="flex items-center">
                        <FUpload v-model="localUser.image_id"
                                 endpoint="/api/user/image"
                                 button-text="Välj profilbild"
                                 class="flex-col w-auto"
                                 upload-name="image"
                                 without-loader
                                 :max-items="1"
                                 @upload-complete="userImageSaved()"
                        />

                        <img v-if="localUser.image.data.thumb_src"
                             :src="localUser.image.data.thumb_src"
                             alt=""
                             class="inline-block rounded-lg h-9 w-9 ml-4 border"
                        >

                        <button v-if="localUser.image.data.thumb_src"
                                class="mt-auto leading-none fabriq-btn btn-link ml-2 text-xs"
                                @click="deleteUserImage()"
                        >
                            Ta bort
                        </button>
                    </div>
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
import FUpload from '~/components/forms/FUpload'
import FLabel from '~/components/forms/FLabel'
import FButton from '~/components/forms/FButton'

export default {
    name: 'ProfileSettings',
    components: { FButton, FLabel, FUpload },
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
            }
        }
    },
    computed: {
        user () {
            return this.$store.getters['user/user']
        }
    },
    activated () {
        this.fetchUser()
    },
    methods: {
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
        }
    }
}
</script>
