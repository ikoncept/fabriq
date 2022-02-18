export default function BroadcastMiddleware ({ next, to, router, store }) {
    // Check if Echo is enabled
    if (!router.app.$echo) {
        return next()
    }
    const Echo = router.app.$echo
    const id = to.params.id
    const pusherKey = window.fabriqCms.pusher.key

    const broadcastName = to.meta.broadcastName
    const capitalizedBroadcastName = broadcastName[0].toUpperCase() + broadcastName.slice(1)

    Echo.channel(`${pusherKey}-${broadcastName}.${id}`)
        .listen(`.${capitalizedBroadcastName}Updated`, (event) => {
            console.log('broadcast heard, updated on id ', event)
        })

    Echo.channel(`${pusherKey}-${broadcastName}.`)
        .listen(`.${capitalizedBroadcastName}Updated`, (event) => {
            console.log('broadcast heard, updated ', event)
        })
        .listen(`.${capitalizedBroadcastName}Deleted`, (event) => {
            console.log('broadcast heard, Deleted ', event)
        })
        .listen(`.${capitalizedBroadcastName}Created`, (event) => {
            console.log('broadcast heard, Created ', event)
        })

    return next()
}
