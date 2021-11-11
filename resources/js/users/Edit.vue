<template>
    <div>
        <UiSectionHeader>
            Redigera användare
            <template #subtitle>
                {{ user.name }}
            </template>
            <template #tools>
                <div class="space-x-4 whitespace-nowrap">
                    <FButton
                        class="px-6 py-2.5 leading-none fabriq-btn btn-link"
                        back-button="users.index"
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
                Generell information
            </template>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-x-6">
                <FInput v-model="user.name"
                        label="Namn"
                        name="name"
                        optional="frivillig"
                        rules="required"
                        placeholder="Skriv namn här..."
                />
                <FInput
                    v-model="user.email"
                    :disabled="currentUser.id === user.id"
                    help-text="Används för inloggning"
                    label="E-postadress"
                    name="email"
                    rules="required|email"
                    type="email"
                />
                <div class="mb-6">
                    <FSelect v-model="chosenRoles"
                             :disabled="currentUser.id === user.id"
                             :help-text=" currentUser.id === user.id ? 'Du kan inte ändra dina egna roller' : ''"
                             label="Roller"
                             :reduce-fn="item => item.name"
                             multiple
                             name="roles"
                             :options="roles"
                             option-label="display_name"
                             value-key="name"
                    />
                </div>
            </div>
        </UiCard>
    </div>
</template>
<script>
import AuthenticatedUser from '~/models/AuthenticatedUser'
import User from '~/models/User'
import Role from '~/models/Role'
export default {
    name: 'UsersEdit',
    data () {
        return {
            id: 0,
            user: {
                name: ''
            },
            roles: [],
            chosenRoles: [],
            currentUser: {
                id: 0
            }
        }
    },
    activated () {
        this.id = this.$route.params.id
        this.fetchUser()
        this.fetchCurrentUser()
        this.fetchRoles()
    },
    methods: {
        async fetchCurrentUser () {
            try {
                const { data } = await AuthenticatedUser.index({ params: { field: 'id' } })
                this.currentUser = data
            } catch (error) {
                console.error(error)
            }
        },
        async fetchUser () {
            try {
                const payload = {
                    params: {
                        include: 'roles'
                    }
                }
                const { data } = await User.show(this.id, payload)
                this.user = data
                this.chosenRoles = this.user.role_list
            } catch (error) {
                console.error(error)
            }
        },
        async updateUser () {
            try {
                this.user.role_list = this.chosenRoles
                await User.update(this.id, { ...this.user })
                this.$toast.success({ title: 'Användaren har uppdaterats' })
            } catch (error) {
                console.error(error)
            }
        },
        async fetchRoles () {
            try {
                const { data } = await Role.index()
                this.roles = data
            } catch (error) {
                console.error(error)
            }
        }
    }
}
</script>
