// import DashboardIcon from '~/icons/DashboardIcon'
import BrowsersIcon from '~/icons/BrowsersIcon'
import BrushFineIcon from '~/icons/BrushFineIcon'
import CalendarIcon from '~/icons/CalendarIcon'
import CameraMovieIcon from '~/icons/CameraMovieIcon'
import DashboardIcon from '~/icons/DashboardIcon'
import FilesIcon from '~/icons/FilesIcon'
import ImagesIcon from '~/icons/ImagesIcon'
import ListTreeIcon from '~/icons/ListTreeIcon'
import NewspaperIcon from '~/icons/NewspaperIcon'
import UsersCrownIcon from '~/icons/UsersCrownIcon'
import UsersGearIcon from '~/icons/UsersGearIcon'

const menuItems = [
    {
        title: 'Dashboard',
        route: 'home.index',
        icon: DashboardIcon,
        thin: true,
        roles: ['*']
    },
    {
        title: 'Sidor',
        route: 'pages.index',
        icon: BrowsersIcon,
        thin: true,
        roles: ['admin']
    },
    {
        title: 'Smarta block',
        route: 'smartBlocks.index',
        icon: BrushFineIcon,
        thin: true,
        roles: ['admin']
    },
    {
        title: 'Nyheter',
        route: 'articles.index',
        thin: true,
        icon: NewspaperIcon,
        roles: ['admin']
    },
    {
        title: 'Kontakter',
        route: 'contacts.index',
        thin: true,
        icon: UsersCrownIcon,
        roles: ['admin']
    },
    {
        title: 'Kalender',
        route: 'calendar.index',
        icon: CalendarIcon,
        thin: true,
        roles: ['admin']
    },
    {
        title: 'Användare',
        route: 'users.index',
        icon: UsersGearIcon,
        thin: true,
        roles: ['admin']
    },
    {
        title: 'Menyer',
        route: 'menus.index',
        icon: ListTreeIcon,
        thin: true,
        roles: ['admin']
    },
    {
        title: 'Bilder',
        route: 'images.index',
        icon: ImagesIcon,
        thin: true,
        roles: ['admin']
    },
    {
        title: 'Videos',
        route: 'videos.index',
        icon: CameraMovieIcon,
        thin: true,
        roles: ['admin']
    },
    {
        title: 'Filer',
        route: 'files.index',
        icon: FilesIcon,
        thin: true,
        roles: ['admin']
    }
]

export default function items () {
    const userRoles = window.fabriqCms.userRoles
    return menuItems.filter(item => {
        if (item.roles.includes('*')) {
            return true
        }
        const matchedRoles = userRoles.filter(element => item.roles.includes(element))
        return matchedRoles.length > 0
    })
}
