import Vue from 'vue'
import store from '~/store'
import router from '~/router'
import i18n from '~/plugins/i18n'
import App from '~/components/App'

import '~/plugins'
import '~/components'

//----------For Flash Messages ----------------//
import VueIziToast from 'vue-izitoast';
import 'izitoast/dist/css/iziToast.min.css';
Vue.use(VueIziToast);

//----------For Date Formate https://github.com/vuejs-community/vue-filter-date-parse----------------//
import VueFilterDateParse from '@vuejs-community/vue-filter-date-parse';
import VueFilterDateFormat from '@vuejs-community/vue-filter-date-format';
Vue.use(VueFilterDateParse);
Vue.use(VueFilterDateFormat);


//----------BootstrapVue---------------------//
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
// Install BootstrapVue
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)

//----------For searchable select option: https://www.npmjs.com/package/vue-search-select -----//
import 'vue-search-select/dist/VueSearchSelect.css'

// for chart: https://github.com/apexcharts/vue-apexcharts
import VueApexCharts from 'vue-apexcharts'
Vue.use(VueApexCharts)

Vue.component('apexchart', VueApexCharts)

Vue.config.productionTip = false



/* eslint-disable no-new */
new Vue({
  i18n,
  store,
  router,
  ...App
})
