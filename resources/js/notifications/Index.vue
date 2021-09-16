<template>
    <div>
        <UiSectionHeader class="mb-4">
            Omnämningar & notiser
        </UiSectionHeader>
        <FTable
            :columns="unseenNotificationColumns"
            :pagination="unseenPagination"
            :rows="unseenNotifications"
            class="mb-8"
            paginated
            clickable-rows
            :options="{ clickableRows: true }"
            @row-clicked="handleRowClicked"
            @change-page="setPage"
            @sort="setSort"
        >
            <template #default="{ row: item, prop }">
                <span v-if="prop == 'created_at'"
                      class="text-xs"
                >{{ item.created_at | localTime(null, true) }}</span>
                <span v-else-if="prop == 'commentInfo'">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-2 h-2 mt-1 mr-4 bg-red-400 rounded-full" />
                        <div v-if="item.notifiable.data.commentable_type === 'App\\Models\\Page'">
                            <div>
                                <div class="text-xs">Sida: {{ item.notifiable.data.page.data.name }}</div>
                            </div>
                            Du blev omnämnd i en kommentar av <span class="font-semibold">{{ item.notifiable.data.user.data.name }}</span>
                        </div>
                        <div v-else
                             class="max-w-full whitespace-normal"
                        >
                            {{ item.content }}
                        </div>
                    </div>
                </span>
                <span v-else-if="prop == 'controls'">
                    <button class="flex items-center focus:outline-none"
                            @click.stop="clearNotification(item.id, true)"
                    >
                        <XMarkIcon class="w-5 h-5" />
                    </button>
                </span>
            </template>
        </FTable>
        <FTable
            :columns="seenNotificationColumns"
            :pagination="seenPagination"
            :rows="seenNotifications"
            class="mb-8"
            paginated
            :options="{ clickableRows: true }"
            @row-clicked="handleRowClicked"
            @change-page="setSeenPage"
            @sort="setSort"
        >
            <template #default="{ row: item, prop }">
                <span v-if="prop == 'created_at'"
                      class="text-xs"
                >{{ item.created_at | localTime(null, true) }}</span>
                <span v-else-if="prop == 'commentInfo'">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-2 h-2 mt-1 mr-4 bg-gray-400 rounded-full" />
                        <div v-if="item.notifiable.data.commentable_type === 'App\\Models\\Page'"
                             class="opacity-80"
                        >
                            <div>
                                <div class="text-xs">Sida: {{ item.notifiable.data.page.data.name }}</div>
                            </div>
                            Du blev omnämnd i en kommentar av <span class="font-semibold">{{ item.notifiable.data.user.data.name }}</span>
                        </div>
                        <div v-else
                             class="max-w-full whitespace-normal opacity-80"
                        >
                            {{ item.content }}
                        </div>
                    </div>
                </span>
                <span v-else-if="prop == 'controls'">
                    <!-- <div class="flex items-center">

                        <XMarkIcon class="w-4 h-4" />
                    </div> -->
                </span>
            </template>
        </FTable>
    </div>
</template>
<script>
import Notification from '~/models/Notification'
export default {
    name: 'NotificationsIndex',
    data () {
        return {
            email: '',
            unseenNotifications: [],
            seenNotifications: [],
            unseenPagination: {},
            unseenQueryParams: {
                number: 20,
                page: 1,
                include: 'notifiable,notifiable.user,notifiable.page',
                'filter[unseen]': true
            },
            seenQueryParams: {
                number: 10,
                page: 1,
                include: 'notifiable,notifiable.user,notifiable.page',
                'filter[seen]': true
            },
            seenPagination: {},
            unseenNotificationColumns: [
                {
                    title: 'Nya notiser',
                    key: 'commentInfo',
                    tdClasses: 'w-full',
                    thClasses: 'w-full'
                },
                {
                    title: '',
                    key: 'controls'
                },
                {
                    title: '',
                    key: 'created_at',
                    sortable: false
                }
            ],
            seenNotificationColumns: [
                {
                    title: 'Hanterade notiser',
                    key: 'commentInfo',
                    tdClasses: 'w-full',
                    thClasses: 'w-full'
                },
                {
                    title: '',
                    key: 'controls'
                },
                {
                    title: '',
                    key: 'created_at',
                    sortable: false
                }
            ]
        }
    },
    activated () {
        this.fetchUnseenItems()
        this.fetchSeenItems()
    },

    methods: {
        async fetchItems () {
            await this.fetchUnseenItems()
            await this.fetchSeenItems()
        },
        async fetchUnseenItems () {
            try {
                const payload = {
                    params: this.unseenQueryParams
                }
                const { data, meta } = await Notification.index(payload)
                this.unseenNotifications = data
                this.unseenPagination = meta.pagination
            } catch (error) {
                console.error(error)
            }
        },
        async fetchSeenItems () {
            try {
                const payload = {
                    params: this.seenQueryParams
                }
                const { data, meta } = await Notification.index(payload)
                this.seenNotifications = data
                this.seenPagination = meta.pagination
            } catch (error) {
                console.error(error)
            }
        },
        handleRowClicked (row) {
            if (row.notifiable.data.commentable_type === 'App\\Models\\Page') {
                this.clearNotification(row.id)
                this.$router.push({ name: 'pages.edit', params: { id: row.notifiable.data.commentable_id }, query: { openComments: true } })
            }
        },
        async clearNotification (id, refresh = false) {
            try {
                await Notification.update(id, {
                    clear: true
                })
                this.$store.dispatch('user/notifications')
                if (refresh) {
                    this.fetchItems()
                }
            } catch (error) {
                console.error(error)
            }
        },
        setSort (sort) {
            this.queryParams.sort = sort
            this.fetchSeenItems()
        },
        setPage (pageNumber) {
            this.unseenQueryParams.page = pageNumber
            this.fetchUnseenItems()
        },
        setSeenPage (pageNumber) {
            this.seenQueryParams.page = pageNumber
            this.fetchSeenItems()
        }
    }
}
</script>
