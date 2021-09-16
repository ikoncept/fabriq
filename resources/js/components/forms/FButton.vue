<template>
    <button class
            :class="{'link': textButton}"
            @click.prevent="handleClick"
    >
        <span class="flex items-center justify-center">
            <span
                :class="{'opacity-0': loading}"
                class="inline-flex items-center transition-opacity duration-200 opacity-1"
            >
                <slot />
            </span>
            <Transition name="slide-fade">
                <div v-if="loading"
                     key="spinner"
                     :class="spinnerColor"
                     class="absolute w-6 h-6"
                >
                    <SpinIcon class="animate-spin" />
                </div>
            </Transition>
        </span>
    </button>
</template>
<script>
export default {
    name: 'FButton',
    props: {
        click: {
            required: false,
            type: Function,
            default: () => {}
        },
        validate: {
            type: Boolean,
            required: false,
            default: false
        },
        delay: {
            required: false,
            type: Number,
            default: 500
        },
        arguments: {
            required: false,
            type: Array,
            default: () => []
        },
        textButton: {
            type: Boolean,
            default: false
        },
        withoutLoader: {
            type: Boolean,
            default: false
        },
        backButton: {
            type: [Boolean, String],
            default: false
        },
        spinnerColor: {
            type: String,
            default: 'text-white'
        }
    },
    data () {
        return {
            loading: false
        }
    },
    computed: {
        lastRoute () {
            return this.$store.getters['routeHistory/lastRoute']
        }
    },
    methods: {
        // /**
        //   * When the button is clicked we start the Ladda button then pass all the
        //   * desired arguments into the "onClick" handler.
        //   *
        //   * @return void
        //   */
        async handleClick () {
            if (this.validate === true && !await this.$validator.validate()) {
                return
            }

            if (this.backButton) {
                this.handleBackClick()
                return
            }

            if (this.withoutLoader) {
                this.click(...this.arguments)
                return
            }

            (typeof this.validate === 'function' ? this.validate() : Promise.resolve()).then(() => {
                this.loading = true

                this.click(...this.arguments)
                    .then(this.stop)
                    .then(typeof this.done === 'function' ? this.done : () => Promise.resolve())
                    .catch((error) => {
                        return this.stop()
                            .then(() => {
                                if (typeof this.failed === 'function') {
                                    this.failed(error)
                                } else {
                                    throw error
                                }
                            })
                    })
            })
        },
        stop () {
            const parameters = arguments

            return new Promise((resolve) => {
                setTimeout(() => {
                    this.loading = false
                    resolve(...parameters)
                }, this.delay)
            })
        },
        handleBackClick () {
            const fallback = this.backButton
            if (!this.lastRoute) {
                this.$router.push({ name: fallback })
            } else {
                this.$router.back()
            }
        }
    }
}
</script>

<style>
.slide-fade-enter-active {
    transition: all 0.2s ease;
}
.slide-fade-leave-active {
    transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}
.slide-fade-enter
/* .slide-fade-leave-active below version 2.1.8 */ {
    transform: scale(2);
    opacity: 0;
}
.slide-fade-leave-to
/* .slide-fade-leave-active below version 2.1.8 */ {
    transform: scale(3);
    opacity: 0;
}
</style>
