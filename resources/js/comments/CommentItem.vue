<template>
    <div class="relative pb-8">
        <span v-if="isLast"
              class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-royal-500"
              aria-hidden="true"
        />
        <div class="relative flex items-start space-x-3">
            <div class="relative">
                <UiAvatar :user="comment.user.data"
                          class="flex items-center justify-center object-cover w-10 h-10 bg-gray-400 rounded-full ring-1 ring-royal-500"
                />
            </div>
            <div class="flex-1 min-w-0">
                <div>
                    <div class="text-sm">
                        <a href="#"
                           class="font-medium text-gray-900"
                           v-text="isOwnedByUser ? 'Du' : comment.user.data.name"
                        />
                        <span class="text-xs text-gray-500">för {{ comment.created_at | localTime(null, true) }}</span> <span v-if="isOwnedByUser"
                                                                                                                              class="text-xs"
                        >&mdash; <span class="text-xs text-red-500 cursor-pointer"
                                       @click="deleteComment"
                                       v-text="isDeleting ? 'Bekräfta' : 'Ta bort'"
                        /></span>
                    </div>
                    <p class="mt-0.5 " />
                </div>
                <div class="pr-4 mt-2 text-sm text-gray-700 ">
                    <div class="prose-sm prose origin-left transform scale-95"
                         v-html="comment.comment"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Comment from '~/models/Comment'
export default {
    name: 'CommentItem',
    props: {
        isLast: {
            type: Boolean,
            default: false
        },
        comment: {
            type: Object,
            required: true,
            default: () => {
                return {
                    user: {
                        data: {}
                    }
                }
            }
        }
    },
    data () {
        return {
            isDeleting: false
        }
    },
    computed: {
        isOwnedByUser () {
            return this.user.email === this.comment.user.data.email
        },
        user () {
            return this.$store.getters['user/user']
        }
    },
    methods: {
        async deleteComment () {
            if (!this.isDeleting) {
                this.isDeleting = true
                return
            }
            try {
                await Comment.destroy(this.comment.id)
                this.$emit('comment-deleted')
                this.$toast.success({ title: 'Kommentaren har raderats!' })
            } catch (error) {
                console.error(error)
                this.isDeleting = false
            }
        }
    }
}
</script>
