// import router from '~/config/router'

// router.beforeEach((to, from, next) => {
//     if (to.matched.some(record => record.meta.requiresAuth)) {
//         // this route requires auth, check if logged in
//         // if not, redirect to login page.
//         if (!auth.loggedIn()) {
//             next({
//                 path: '/login',
//                 query: { redirect: to.fullPath }
//             })
//         } else {
//             next()
//         }
//     } else {
//         next() // make sure to always call next()!
//     }
// })

export default function roles ({ next, to, router, store, vm }) {
    const userRoles = window.fabriqCms.userRoles

    if (to.meta.roles.includes('*')) {
        return next()
    }
    const matchedRoles = userRoles.filter(element => to.meta.roles.includes(element))
    if (matchedRoles.length <= 0) {
        vm.$toast.warning({ title: 'Stopp i lagens namn!', message: 'Du saknar behörighet att visa sidan' })
        return
    }

    return next()
}
