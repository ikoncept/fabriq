export default function PresenceMiddleware ({ next, to, router, store }) {
    const Echo = router.app.$echo
    const id = to.params.id
    const pusherAppId = window.fabriqCms.pusher.appId
    const roomName = to.name
    const identifier = roomName + '.' + id

    // console.log(id, roomName, identifier)
    // return next()

    // Echo.private(identifier)
    //     .listen('.Ikoncept\\Fabriq\\Events\\ArticleUpdated', (event) => {
    //         console.log(event.article)
    //         this.fetchArticle()
    //     })
    Echo.join(pusherAppId + '.presence.' + identifier)
        .here((users) => {
            const object = {}
            object[identifier] = users
            store.commit('echo/SET_USERS_HERE', object)
        })
        .joining((user) => {
            store.commit('echo/USER_JOINING', { identifier: identifier, user: user })
        })
        .leaving((user) => {
            store.commit('echo/USER_LEAVING', { identifier: identifier, user: user })
        })
    // vm.$toast.warning({ title: 'Stopp i lagens namn!', message: 'Du saknar behörighet att visa sidan' })
    // const userRoles = window.fabriqCms.userRoles

    // if (to.meta.roles.includes('*')) {
    //     return next()
    // }
    // const matchedRoles = userRoles.filter(element => to.meta.roles.includes(element))
    // if (matchedRoles.length <= 0) {
    //     vm.$toast.warning({ title: 'Stopp i lagens namn!', message: 'Du saknar behörighet att visa sidan' })
    //     return
    // }

    return next()
}
