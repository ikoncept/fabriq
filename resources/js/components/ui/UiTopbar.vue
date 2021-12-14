<template>
    <div class="absolute z-20 flex justify-between w-full h-16 bg-white border shadow lg:hidden">
        <button
            class="px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-gold-500 lg:hidden"
            @click="openMenu"
        >
            <span class="sr-only">Open sidebar</span>
            <!-- Heroicon name: menu-alt-2 -->
            <svg
                aria-hidden="true"
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M4 6h16M4 12h16M4 18h7"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                />
            </svg>
        </button>
        <div class="flex justify-between px-4">
            <div class="flex items-center ml-4 md:ml-6">
                <!-- Profile dropdown -->
                <UiDropdown ref="profileDropdown"
                            alignment="top-right"
                >
                    <template #dropdown>
                        <div
                            aria-labelledby="user-menu"
                            aria-orientation="vertical"
                            role="menu"
                            class="w-48"
                        >
                            <a
                                class="block px-4 py-2 text-sm link hover:bg-gray-100"
                                href="#"
                                role="menuitem"
                            >Your Profile</a>

                            <a
                                class="block px-4 py-2 text-sm link hover:bg-gray-100"
                                href="#"
                                role="menuitem"
                            >Settings</a>

                            <LogoutForm class="w-full">
                                <button class="block w-full px-4 py-2 text-sm text-left link hover:bg-gray-100">
                                    Logga ut
                                </button>
                            </LogoutForm>
                        </div>
                    </template>

                    <!-- <link-icon class="w-3 h-3" /> -->
                    <div class="">
                        <button
                            id="user-menu"
                            aria-haspopup="true"
                            class="flex items-center max-w-xs text-sm bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold-300"
                            @click="showDropdown = ! showDropdown"
                        >
                            <span class="sr-only">Open user menu</span>
                            <UiAvatar :user="user"
                                      class="w-8 h-8 rounded-lg"
                            />
                        </button>
                    </div>
                </UiDropdown>
                <!-- <div v-click-outside="closeDropdown" class="relative ml-3">
                    <transition
                        enter-active-class="transition duration-100 ease-out"
                        enter-class="transform scale-95 opacity-0"
                        enter-to-class="transform scale-100 opacity-100"
                        leave-active-class="transition duration-75 ease-in"
                        leave-class="transform scale-100 opacity-100"
                        leave-to-class="transform scale-95 opacity-0"
                    >
                        <div
                            v-if="showDropdown"
                            aria-labelledby="user-menu"
                            aria-orientation="vertical"
                            class="absolute right-0 w-48 py-1 mt-2 origin-top-right bg-white border rounded-md shadow-lg ring-1 ring-gray-300 ring-opacity-5"
                            role="menu"
                        >
                            <a
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="#"
                                role="menuitem"
                            >Your Profile</a>

                            <a
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="#"
                                role="menuitem"
                            >Settings</a>

                            <logout-form />
                        </div>
                    </transition>
                </div> -->
            </div>
        </div>
    </div>
</template>
<script>
import * as types from '~/store/mutation-types'
import LogoutForm from '~/components/LogoutForm'
export default {
    name: 'UiTopbar',
    components: { LogoutForm },
    data () {
        return {
            csrfToken: '',
            showDropdown: false
        }
    },
    computed: {
        user () {
            return this.$store.getters['user/user']
        },
        menuOpen () {
            return this.$store.getters['ui/menuOpen']
        }

    },

    methods: {
        closeDropdown () {
            this.showDropdown = false
        },
        closeMenu () {
            this.$store.commit('ui/' + types.CLOSE_MENU)
        },
        openMenu () {
            this.$store.commit('ui/' + types.OPEN_MENU)
        },
        toggleMenu () {
            this.$store.commit('ui/' + types.TOGGLE_MENU)
        }
    }
}
</script>
