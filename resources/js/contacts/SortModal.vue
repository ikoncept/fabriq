<template>
    <FModal
        v-model="show"
        name="sort-contact-modal"
        width="max-w-5xl"
        overflow="overflow-auto"
        :esc-to-close="false"
        @before-open="initModal"
    >
        <template #title>
            <div class="flex items-center justify-between flex-1">
                <div class="flex items-end space-x-4">
                    <span
                        class="text-xl text-gray-700"
                        v-text="'Sortera kontakter'"
                    />
                </div>
            </div>
        </template>
        <div>
            <Draggable
                v-model="contacts"
                handle=".handle"
                tag="ul"
                v-bind="dragOptions"
                class="list-group flex flex-col"
                @end="saveSortOrder"
            >
                <li
                    v-for="contact in contacts"
                    :key="contact.id"
                    class="flex  h-12 text-sm items-center gap-x-2 border-b border-r border-l first-of-type:border-t"
                >
                    <GripVerticalIcon
                        class="block w-6 h-6 text-gray-300 cursor-move handle"
                    />
                    {{ contact.name }} <UiBadge>{{ contact.sortindex }}</UiBadge>
                </li>
            </Draggable>
        </div>
    </FModal>
</template>

<script>
import Contact from '@/models/Contact'
import axios from 'axios'
import Draggable from 'vuedraggable'

export default {
    name: 'SortModal',
    components: {
        Draggable
    },
    props: {
        item: {
            type: Object,
            default: () => { }
        }
    },
    data()  {
        return {
            show: false,
            contacts: []
        }
    },
    computed: {
        dragOptions () {
            return {
                animation: 200,
                group: 'description',
                ghostClass: 'ghost',
            }
        },
    },
    methods: {
        async fetchContacts () {
            try {
                const payload = {
                    params: {
                        sort: 'sortindex',
                        number: 1500,
                    }
                }

                const { data, meta } = await Contact.index(payload)
                this.contacts = data
            } catch (error) {
                console.error(error)
            }
        },
        async saveSortOrder(stuff) {
            console.log(stuff, this.contacts)
            this.contacts.forEach((item, index) => {
                item.sortindex = (index + 1) * 10
            })
            try {
                await axios.post('/api/admin/contacts/sort-contacts', { contacts: this.contacts })
                this.$emit('updated')
                this.$toast.success({
                    title: 'Kontakterna har sorterats!',
                })
            } catch (error) {
                this.$toast.error({
                    title: 'Kontakterna kunde inte sorteras',
                })
                console.error(error)
            }
        },
        async initModal (parameters) {
            try {
                const promises = [
                    this.fetchContacts()
                ]
                await Promise.all(promises)
            } catch (error) {
                console.error(error)
            }
        },
    }
}
</script>
