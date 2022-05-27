export default function BroadcastMiddleware ({ next, to, router, store }) {
    // Check if Echo is enabled
    if (!router.app.$echo) {
        return next()
    }
    const Echo = router.app.$echo
    const id = to.params.id
    const wsPrefix = window.fabriqCms.pusher.ws_prefix

    const broadcastName = to.meta.broadcastName
    const capitalizedBroadcastName = broadcastName[0].toUpperCase() + broadcastName.slice(1)

    // Listen to model events
    Echo.channel(`${wsPrefix}-${broadcastName}.${id}`)
        .listen(`.${capitalizedBroadcastName}Updated`, (event) => {
            if (store.getters['user/user'].id !== event.model.updated_by) {
                router.app.$eventBus.$emit(`${broadcastName}-updated-echo`, event)
            }
        })

    Echo.channel(`${wsPrefix}-${broadcastName}.`)
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
