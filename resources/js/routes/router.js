import Vue from 'vue'
import VueRouter from 'vue-router'
import routes from '~/routes/fabriq-routes'
import userRoutes from '~/routes/routes'
import store from '~/store'
import * as types from '~/store/mutation-types'

Vue.use(VueRouter)
const router = createRouter()

export default router

/**
 * Create a new router instance.
 *
 * @return {VueRouter}
 */
function createRouter () {
    const router = new VueRouter({
        mode: 'history',
        routes: [...routes, ...userRoutes]
    })

    router.beforeEach(beforeEach)
    router.afterEach(afterEach)

    return router
}

function afterEach (to, from) {
    store.commit('ui/CLOSE_MENU')
    const Echo = router.app.$echo
    if (Echo) {

        const id = from.params.id
        const roomName = from.name
        const identifier = roomName + '.' + id
        const wsPrefix = window.fabriqCms.pusher.ws_prefix
        Echo.leave(wsPrefix + '.presence.' + identifier)
        store.commit('echo/USER_LEAVING', { identifier: identifier, user: store.getters['user/user'] })

        if (from.meta.broadcastName) {
            const broadcastName = from.meta.broadcastName
            const capitalizedBroadcastName = broadcastName[0].toUpperCase() + broadcastName.slice(1)

            Echo.channel(`${wsPrefix}-${broadcastName}.${id}`)
                .stopListening(`.${capitalizedBroadcastName}Updated`)

            Echo.channel(`${wsPrefix}-${broadcastName}.`)
                .stopListening(`.${capitalizedBroadcastName}Updated`)
                .stopListening(`.${capitalizedBroadcastName}Deleted`)
                .stopListening(`.${capitalizedBroadcastName}Created`)
        }
    }
}

function beforeEach (to, from, next) {
    store.commit('routeHistory/' + types.SET_FROM_ROUTE, from.name)
    if (to.meta.middleware) {
        const middleware = Array.isArray(to.meta.middleware) ? to.meta.middleware : [to.meta.middleware]

        const context = {
            from,
            next,
            router,
            to,
            store
        }
        const nextMiddleware = nextFactory(context, middleware, 1)

        return middleware[0]({ ...context, next: nextMiddleware })
    }

    next()
}

// Creates a `nextMiddleware()` function which not only
// runs the default `next()` callback but also triggers
// the subsequent Middleware function.
function nextFactory (context, middleware, index) {
    const subsequentMiddleware = middleware[index]
    // If no subsequent Middleware exists,
    // the default `next()` callback is returned.
    if (!subsequentMiddleware) return context.next

    return (...parameters) => {
        // Run the default Vue Router `next()` callback first.
        context.next(...parameters)
        // Then run the subsequent Middleware with a new
        // `nextMiddleware()` callback.
        const nextMiddleware = nextFactory(context, middleware, index + 1)
        subsequentMiddleware({ ...context, next: nextMiddleware })
    }
}
