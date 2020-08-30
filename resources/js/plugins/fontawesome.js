import Vue from 'vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'


// import { } from '@fortawesome/free-regular-svg-icons'

import {
  faUser, faLock, faSignOutAlt, faCog, faTrashAlt, faEdit, faEye, faLanguage, faUserSecret, faUserNinja, faHistory, faPlus, faUtensils,
  faBalanceScaleLeft, faEquals, faGreaterThan, faGreaterThanEqual, faMapMarkedAlt, faUsersCog
} from '@fortawesome/free-solid-svg-icons'

import {
  faGithub
} from '@fortawesome/free-brands-svg-icons'

library.add(
  faUser, faLock, faSignOutAlt, faCog, faGithub, faTrashAlt, faEdit, faEye, faLanguage, faUserSecret, faUserNinja, faHistory, faPlus,
  faUtensils, faBalanceScaleLeft, faEquals, faGreaterThan, faGreaterThanEqual, faMapMarkedAlt, faUsersCog
)

Vue.component('fa', FontAwesomeIcon)
