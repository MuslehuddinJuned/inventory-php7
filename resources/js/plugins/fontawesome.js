import Vue from 'vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'


// import { } from '@fortawesome/free-regular-svg-icons'

import {
  faUser, faLock, faSignOutAlt, faCog, faTrashAlt, faEdit, faSave, faEye, faLanguage, faUserSecret, faUserNinja, faHistory, faPlus, faUtensils,
  faBalanceScaleLeft, faEquals, faGreaterThan, faGreaterThanEqual, faMapMarkedAlt, faUsersCog, faMoneyCheckAlt, faFileInvoiceDollar,
  faHandHoldingUsd, faMoneyBillAlt
} from '@fortawesome/free-solid-svg-icons'

import {
  faGithub, faCcAmazonPay
} from '@fortawesome/free-brands-svg-icons'

library.add(
  faUser, faLock, faSignOutAlt, faCog, faGithub, faTrashAlt, faEdit, faSave, faEye, faLanguage, faUserSecret, faUserNinja, faHistory, faPlus,
  faUtensils, faBalanceScaleLeft, faEquals, faGreaterThan, faGreaterThanEqual, faMapMarkedAlt, faUsersCog, faMoneyCheckAlt, faFileInvoiceDollar,
  faHandHoldingUsd, faMoneyBillAlt, faCcAmazonPay
)

Vue.component('fa', FontAwesomeIcon)
