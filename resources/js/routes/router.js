import Vue from 'vue'
import VueRouter from 'vue-router'
import routes from '~/routes/fabriq-routes'
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
        routes
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
        const pusherAppId = window.fabriqCms.pusher.appId
        Echo.leave(pusherAppId + '.presence.' + identifier)
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
