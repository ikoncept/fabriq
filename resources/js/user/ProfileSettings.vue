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
                <div v-if="localUser.email_verified_at"
                     class="col-span-4"
                >
                    <FLabel>
                        <div>
                            E-postadressen är bekräftad
                        </div>
                    </FLabel>
                    <div class="flex items-center">
                        <CheckIcon thin
                                   class="w-6 h-6 mr-2"
                        />
                        <div class="text-sm text-gray-400">
                            {{ localUser.email_verified_at | localTime }}
                        </div>
                    </div>
                </div>
                <div v-else
                     class="col-span-4"
                >
                    <FLabel>
                        <div>
                            <div class="flex items-center">
                                E-postadressen är inte verifierad
                            </div>
                        </div>
                    </FLabel>
                    <FButton class="py-2.5 px-6 fabriq-btn btn-royal"
                             :click="sendVerificationRequest"
                    >
                        Skicka verifieringsförfrågan
                    </FButton>
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
    data () {
        return {
            localUser: {},
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
        }
    }
}
</script>
