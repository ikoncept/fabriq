/* Set up using Vue 2 */
import Vue from 'vue'

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import { faUser } from '@fortawesome/free-regular-svg-icons'

/* add icons to the library */
library.add(faUser)

/* add font awesome icon component */
Vue.component('FontAwesomeIcon', FontAwesomeIcon)
