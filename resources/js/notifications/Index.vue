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
            :options="{ clickableRows: true, emptyText: 'Inga notiser att hantera! ☕' }"
            @row-clicked="handleRowClicked"
            @change-page="setPage"
            @sort="setSort"
        >
            <template #default="{ row: item, prop }">
                <span v-if="prop == 'commentInfo'">
                    <div class="flex items-start ">
                        <div class="flex-shrink-0 w-2 h-2 mt-1 mr-4 bg-red-400 rounded-full" />
                        <div v-if="item.notifiable.data.commentable_type.includes('Page')">
                            <PageComment :item="item"
                                         :cleared="false"
                            />
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
                <span v-if="prop == 'commentInfo'">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-2 h-2 mt-1 mr-4 bg-gray-400 rounded-full" />
                        <div v-if="item.notifiable.data.commentable_type.includes('Page')"
                             class="opacity-80"
                        >
                            <PageComment :item="item"
                                         :cleared="true"
                            />
                        </div>
                        <div v-else
                             class="max-w-full whitespace-normal opacity-80"
                        >
                            {{ item.content }}
                            <div class="text-xs font-semibold">
                                {{ item.created_at | localTime(null, true) }}
                            </div>
                        </div>
                    </div>
                </span>
            </template>
        </FTable>
    </div>
</template>
<script>
import Notification from '~/models/Notification'
import PageComment from '~/notifications/PageComment'
export default {
    name: 'NotificationsIndex',
    components: {
        PageComment
    },
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
                    tdClasses: 'w-full ',
                    thClasses: 'w-full bg-royal-500 rounded-tl text-gold-200'
                },
                {
                    title: '',
                    key: 'controls',
                    thClasses: 'w-full bg-royal-500  text-gold-300 rounded-tr'
                }
            ],
            seenNotificationColumns: [
                {
                    title: 'Hanterade notiser',
                    key: 'commentInfo',
                    tdClasses: 'w-full',
                    thClasses: 'w-full bg-royal-500 rounded-t text-gold-200'
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
            if (row.notifiable.data.commentable_type.includes('Page')) {
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
<style>
blockquote {
    overflow: hidden;
    white-space: nowrap;
}
blockquote p {
    /* white-space: nowrap; */
    display: inline;
}
</style>
