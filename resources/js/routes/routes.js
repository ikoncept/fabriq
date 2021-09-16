import ArticlesEdit from '~/articles/Edit'
import ArticlesIndex from '~/articles/Index'
import CalendarIndex from '~/calendar/Index'
import ContactEdit from '~/contacts/Edit'
import ContactIndex from '~/contacts/Index'
import Dashboard from '~/dashboard/Dashboard.vue'
import NotFound from '~/errors/NotFound.vue'
import FilesIndex from '~/files/Index'
import Home from '~/Home'
import ImagesIndex from '~/images/Index'
import MenusEdit from '~/menus/Edit'
import MenusIndex from '~/menus/Index'
import roles from '~/middleware/roles-middleware'
import NotificationIndex from '~/notifications/Index'
import PagesEdit from '~/pages/Edit'
import PagesIndex from '~/pages/Index'
import SmartBlocksEdit from '~/smart-blocks/Edit'
import SmartBlocksIndex from '~/smart-blocks/Index'
import ProfileSettings from '~/user/ProfileSettings'
import UsersEdit from '~/users/Edit'
import UsersIndex from '~/users/Index'
import VideosIndex from '~/videos/Index'

export default [
    {
        path: '/',
        redirect: '/dashboard'
    },
    {
        path: '/dashboard',
        name: 'home.index',
        component: Home,
        meta: {
            middleware: roles,
            roles: ['*', 'restaurant']
        }
    },
    {
        path: '/profile/settings',
        name: 'profile.settings',
        component: ProfileSettings,
        meta: {
            middleware: roles,
            roles: ['*']
        }
    },
    {
        path: '/dashboard',
        name: 'dashboard.index',
        component: Dashboard,
        meta: {
            middleware: roles,
            roles: ['admin']
        }
    },
    {
        path: '/pages',
        name: 'pages.index',
        component: PagesIndex,
        meta: {
            middleware: roles,
            roles: ['admin']
        }
    },
    {
        path: '/pages/:id/edit',
        name: 'pages.edit',
        component: PagesEdit,
        meta: {
            middleware: roles,
            roles: ['admin'],
            commentable: true
        }
    },
    {
        path: '/users',
        name: 'users.index',
        component: UsersIndex,
        meta: {
            middleware: roles,
            roles: ['admin']
        }
    },
    {
        path: '/users/:id/edit',
        name: 'users.edit',
        component: UsersEdit,
        meta: {
            middleware: roles,
            roles: ['admin']
        }
    },
    {
        path: '/notifications',
        name: 'notifications.index',
        component: NotificationIndex,
        meta: {
            middleware: roles,
            roles: ['admin']
        }
    },
    {
        path: '/menus',
        name: 'menus.index',
        component: MenusIndex,
        meta: {
            middleware: roles,
            roles: ['admin']
        }
    },
    {
        path: '/menus/:id/edit',
        name: 'menus.edit',
        component: MenusEdit,
        meta: {
            middleware: roles,
            roles: ['admin']
        }
    },
    {
        path: '/media/images',
        name: 'images.index',
        component: ImagesIndex,
        meta: {
            middleware: roles,
            roles: ['admin']
        }
    },
    {
        path: '/articles',
        name: 'articles.index',
        component: ArticlesIndex,
        meta: {
            middleware: roles,
            roles: ['admin']
        }
    },
    {
        path: '/articles/:id/edit',
        name: 'articles.edit',
        component: ArticlesEdit,
        meta: {
            middleware: roles,
            roles: ['admin']
        }
    },
    {
        path: '/calendar',
        name: 'calendar.index',
        component: CalendarIndex,
        meta: {
            middleware: roles,
            roles: ['admin']
        }
    },
    {
        path: '/contacts',
        name: 'contacts.index',
        component: ContactIndex,
        meta: {
            middleware: roles,
            roles: ['admin']
        }
    },
    {
        path: '/contacts/:id/edit',
        name: 'contacts.edit',
        component: ContactEdit,
        meta: {
            middleware: roles,
            roles: ['admin']
        }
    },
    {
        path: '/media/files',
        name: 'files.index',
        component: FilesIndex,
        meta: {
            middleware: roles,
            roles: ['admin']
        }
    },
    {
        path: '/media/videos',
        name: 'videos.index',
        component: VideosIndex,
        meta: {
            middleware: roles,
            roles: ['admin']
        }
    },
    {
        path: '/smart-blocks',
        name: 'smartBlocks.index',
        component: SmartBlocksIndex,
        meta: {
            middleware: roles,
            roles: ['admin']
        }
    },
    {
        path: '/smart-blocks/:id/edit',
        name: 'smartBlocks.edit',
        component: SmartBlocksEdit,
        meta: {
            middleware: roles,
            roles: ['admin']
        }
    },
    {
        name: 'errors.404',
        path: '*',
        component: NotFound
    }
]
