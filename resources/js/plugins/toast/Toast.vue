<template>
    <Transition
        enter-active-class="transition ease-out transform duration-600"
        enter-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-3"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="duration-100 ease-in transform"
        leave-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-to-class="translate-y-1 opacity-0 sm:translate-x-3 sm:translate-y-0"
    >
        <div
            v-show="isActive"
            :class="[positionClasses]"
            class="flex px-4 py-2 duration-150 pointer-events-none sm:p-3"
            role="alert"
            @click="close"
            @mouseleave="toggleTimer(false)"
            @mouseover="toggleTimer(true)"
        >
            <div
                :class="{'bg-gray-800': dark, 'bg-white': ! dark}"
                class="relative w-full max-w-sm rounded-lg shadow-lg pointer-events-auto"
            >
                <div
                    :class="{'ring-2 ring-red-500 ring-inset': type == 'error'}"
                    class="overflow-hidden rounded-lg shadow-xs"
                >
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <span class="text-2xl">{{ emoji }}</span>
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p
                                    :class="{'text-gray-900': ! dark, 'text-gray-50': dark}"
                                    class="text-sm font-bold leading-5"
                                >
                                    {{ title }}
                                </p>
                                <p
                                    v-if="message"
                                    :class="{'text-500-900': ! dark, 'text-gray-200': dark}"
                                    class="mt-1 text-xs font-medium leading-5"
                                    v-html="message"
                                />
                                <div v-if="buttonText"
                                     class="mt-2"
                                >
                                    <button
                                        class="text-sm font-medium leading-5 transition duration-150 ease-in-out text-gold-400 hover:text-gold-500 focus:outline-none focus:underline"
                                        @click="whenClicked"
                                    >
                                        {{ buttonText }}
                                    </button>
                                    <button
                                        :class="{'text-gray-700': ! dark, 'text-gray-300': dark}"
                                        class="ml-6 text-sm font-medium leading-5 transition duration-150 ease-in-out hover:text-gray-500 focus:outline-none focus:underline"
                                    >
                                        {{ dismissText }}
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-shrink-0 ml-4">
                                <button
                                    class="inline-flex text-lg text-gray-200 transition duration-150 ease-in-out focus:outline-none focus:text-gray-500"
                                    @click="close"
                                >
                                    <XMarkIcon class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script>
import { removeElement } from './helpers.js'
import Timer from './timer.js'
import Positions from './positions.js'
import eventBus from './bus.js'
import config from '~/config/config'
import './themes/toast.css'

export default {
    name: 'Toast',
    props: {
        title: {
            type: String,
            required: true
        },
        message: {
            type: String,
            default: null
        },
        type: {
            type: String,
            default: 'default'
        },
        buttonText: {
            type: String,
            default: null
        },
        icon: {
            type: String,
            default: null
        },
        position: {
            type: String,
            default: Positions.BOTTOM_RIGHT
        },
        duration: {
            type: Number,
            default: 3000
        },
        dismissible: {
            type: Boolean,
            default: true
        },
        dismissText: {
            type: String,
            required: true
        },
        onClose: {
            type: Function,
            default: () => { }
        },
        onClick: {
            type: Function,
            default: () => null
        },
        queue: Boolean,
        pauseOnHover: {
            type: Boolean,
            default: true
        },
        dark: {
            type: Boolean,
            default: false
        }
    },
    data () {
        return {
            isActive: false,
            parent: null,
            isHovered: false
        }
    },
    computed: {
        correctParent () {
            return this.parent
        },
        emoji () {
            if (!this.type && !this.icon) {
                return null
            }

            if (config.toasts[this.type].icons) {
                return config.toasts[this.type].icons[Math.floor(Math.random() * config.toasts[this.type].icons.length)]
            }

            if (!this.icon && this.type !== 'default') {
                return config.toasts[this.type].icon
            }

            return this.icon
        },
        positionClasses () {
            if (this.position === Positions.TOP) {
                return 'sm:items-start sm:justify-center items-end justify-center'
            }
            if (this.position === Positions.TOP_RIGHT) {
                return 'sm:items-start sm:justify-end items-end justify-center'
            }
            if (this.position === Positions.TOP_LEFT) {
                return 'sm:items-start sm:justify-start items-end justify-center'
            }
            if (this.position === Positions.BOTTOM) {
                return 'sm:items-end sm:justify-center items-end justify-center'
            }
            if (this.position === Positions.BOTTOM_RIGHT) {
                return 'sm:items-end sm:justify-end items-end justify-center'
            }
            if (this.position === Positions.BOTTOM_LEFT) {
                return 'sm:items-end sm:justify-start items-end justify-center'
            }

            return 'sm:items-start sm:justify-center items-end justify-center'
        }
    },
    beforeMount () {
        this.setupContainer()
    },
    mounted () {
        this.showNotice()

        eventBus.$on('toast-clear', this.close)
    },
    beforeDestroy () {
        eventBus.$off('toast-clear', this.close)
    },
    methods: {
        setupContainer () {
            let verticalClass = 'top'
            if (this.position.includes('bottom')) {
                verticalClass = 'bottom'
            }
            this.parent = document.querySelector('.notices.is-' + verticalClass)

            if (!this.parent) {
                this.parent = document.createElement('div')
                this.parent.className = 'notices is-' + verticalClass
            }

            const container = document.body
            container.appendChild(this.parent)
        },

        shouldQueue () {
            if (!this.queue) return false

            return this.parent.childElementCount > 0
        },

        close () {
            this.timer.stop()
            clearTimeout(this.queueTimer)
            this.isActive = false

            // Timeout for the animation complete before destroying
            setTimeout(() => {
                // this.onClose.apply(null, arguments)
                this.$destroy()
                removeElement(this.$el)
            }, 1500)
        },

        showNotice () {
            if (this.shouldQueue()) {
                // Call recursively if should queue
                this.queueTimer = setTimeout(this.showNotice, 250)
                return
            }
            this.correctParent.insertAdjacentElement('afterbegin', this.$el)
            this.isActive = true

            this.timer = new Timer(this.close, this.duration)
        },

        whenClicked () {
            if (!this.dismissible) return
            this.onClick.apply(null, arguments)
            this.close()
        },
        toggleTimer (newValue) {
            if (!this.pauseOnHover) return
            newValue ? this.timer.pause() : this.timer.resume()
        }
    }
}
</script>
