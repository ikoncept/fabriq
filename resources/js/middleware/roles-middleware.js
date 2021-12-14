export default function RolesMiddleware ({ next, to, router, store, vm }) {
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
