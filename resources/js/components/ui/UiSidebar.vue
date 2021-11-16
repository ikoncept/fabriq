<template>
    <div class="lg:hidden">
        <div :class="{'pointer-events-none': ! menuOpen}"
             class="fixed inset-0 z-40 flex"
        >
            <Transition
                enter-active-class="transition-opacity duration-300 ease-linear"
                enter-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-opacity duration-300 ease-linear"
                leave-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="menuOpen"
                     aria-hidden="true"
                     class="fixed inset-0"
                >
                    <div class="absolute inset-0 bg-gray-600 opacity-75" />
                </div>
            </Transition>

            <Transition
                enter-active-class="transition duration-300 ease-in-out transform"
                enter-class="-translate-x-full"
                enter-to-class="translate-x-0"
                leave-active-class="transition duration-300 ease-in-out transform"
                leave-class="translate-x-0"
                leave-to-class="-translate-x-full"
            >
                <div
                    v-if="menuOpen"
                    class="relative flex flex-col flex-1 w-full max-w-xs pt-5 pb-4 bg-white"
                >
                    <div class="absolute top-0 right-0 pt-2 -mr-12">
                        <button
                            class="flex items-center justify-center w-10 h-10 ml-1 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                            @click="closeMenu"
                        >
                            <span class="sr-only">Close sidebar</span>
                            <!-- Heroicon name: x -->
                            <svg
                                aria-hidden="true"
                                class="w-6 h-6 text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M6 18L18 6M6 6l12 12"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                />
                            </svg>
                        </button>
                    </div>
                    <div class="flex items-center flex-shrink-0 px-4">
                        <img
                            alt="Fabriq CMS Logotyp"
                            class="w-full px-12"
                            src="/dist/images/fabriq-cms-logo.svg"
                        >
                    </div>
                    <div class="flex-1 h-0 mt-5 overflow-y-auto">
                        <nav class="px-2 space-y-1">
                            <RouterLink
                                v-for="(item, index) in menuItems"
                                :key="`ds${index}`"
                                :to="{ name: item.route }"
                                class="text-base sidebar-item"
                            >
                                <Component
                                    :is="item.icon"
                                    class="w-6 h-6 mr-4 text-gray-400 group-hover:text-gray-500"
                                />
                                {{ item.title }}
                            </RouterLink>
                        </nav>
                    </div>
                </div>
            </Transition>
            <div aria-hidden="true"
                 class="flex-shrink-0 w-14"
            >
                <!-- Dummy element to force sidebar to shrink to fit close icon -->
            </div>
        </div>
    </div>
</template>

<script>
import * as types from '~/store/mutation-types'
export default {
    name: 'UiSidebar',
    computed: {
        menuOpen () {
            return this.$store.getters['ui/menuOpen']
        },
        menuItems () {
            return this.$store.getters['menu/menuItems']
        }
    },
    methods: {
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
