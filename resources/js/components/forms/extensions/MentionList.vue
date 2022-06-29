<template>
    <div class="items">
        <button
            v-for="(item, index) in items"
            :key="index"
            class="item"
            :class="{ 'is-selected': index === selectedIndex }"
            @click="selectItem(index)"
        >
            {{ item.name }}
        </button>
    </div>
</template>

<script>
export default {
    props: {
        items: {
            type: Array,
            required: true
        },

        command: {
            type: Function,
            required: true
        }
    },

    data () {
        return {
            selectedIndex: 0
        }
    },

    watch: {
        items () {
            this.selectedIndex = 0
        }
    },

    methods: {
        onKeyDown ({ event }) {
            if (event.key === 'ArrowUp') {
                this.upHandler()
                return true
            }

            if (event.key === 'ArrowDown') {
                this.downHandler()
                return true
            }

            if (event.key === 'Enter') {
                this.enterHandler()
                return true
            }

            return false
        },

        upHandler () {
            this.selectedIndex = ((this.selectedIndex + this.items.length) - 1) % this.items.length
        },

        downHandler () {
            this.selectedIndex = (this.selectedIndex + 1) % this.items.length
        },

        enterHandler () {
            this.selectItem(this.selectedIndex)
        },

        selectItem (index) {
            const item = this.items[index]

            if (item) {
                this.command({ id: item.name, 'data-email': item.email })
            }
        }
    }
}
</script>

