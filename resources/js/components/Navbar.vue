<template>
  <div>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
      <div class="container">
        <img class="img-responsive mr-2" src="/favicon.ico" width="30px" alt="SUSTipe">
        <router-link :to="{ name: user ? 'home' : 'welcome' }" class="navbar-brand">
          SUSTipe
          <!-- SUSTipe -->
        </router-link>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false">
          <span class="navbar-toggler-icon" />
        </button>

        <div id="navbarToggler" class="collapse navbar-collapse">
          <ul class="navbar-nav">
            <locale-dropdown />
            <li v-if="user && module_no == 1 && (checkRoles('InventoryItem_View') || checkRoles('ItemReceive_View') || checkRoles('ItemIssue_View') || checkRoles('requisition_View'))" class="nav-item dropdown">
              <a id="inventory" class="nav-link dropdown-toggle" href="#"  role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><b-icon icon="cart4"></b-icon> {{ $t('Inventory') }}</a>
              <div class="dropdown-menu dropdown-menu-left" aria-labelledby="inventory">
                <router-link v-if="checkRoles('InventoryItem_View')" :to="{ name: 'inventory.InventoryList' }" class="dropdown-item pl-3">                
                  <b-icon icon="cart-check"></b-icon>
                  {{ $t('InventoryItem') }}
                </router-link>
                <!-- <router-link v-if="checkRoles('InventoryItem_View')" :to="{ name: 'inventory.InventoryListEtd' }" class="dropdown-item pl-3">                
                  <b-icon icon="cart3"></b-icon>
                  {{ $t('InventoryItem') + ' (ETD)' }}
                </router-link> -->
                <router-link v-if="checkRoles('ItemReceive_View')" :to="{ name: 'inventory.InventoryReceive' }" class="dropdown-item pl-3">
                  <b-icon icon="cart-plus"></b-icon>
                  {{ $t('ItemReceive') }}
                </router-link>
                <router-link v-if="checkRoles('ItemIssue_View')" :to="{ name: 'inventory.InventoryIssue' }" class="dropdown-item pl-3">
                  <b-icon icon="cart-dash"></b-icon>
                  {{ $t('ItemIssue') }}
                </router-link>
                <router-link v-if="checkRoles('requisition_View')" :to="{ name: 'inventory.RequisitionList' }" class="dropdown-item pl-3">
                  <b-icon icon="basket2-fill"></b-icon>
                  {{ $t('requisition') }}
                </router-link>
                <router-link v-if="checkRoles('InventoryItem_View')" :to="{ name: 'inventory.BalanceSheet' }" class="dropdown-item pl-3">
                  <fa icon="balance-scale-left" fixed-width/>
                  {{ $t('balance_sheet') }}
                </router-link>
              </div>
            </li>
            <li v-if="user && module_no == 1 && (checkRoles('product_details_View') || checkRoles('po_list_View') || checkRoles('monitor_etd_View'))" class="nav-item dropdown">
              <a id="product" class="nav-link dropdown-toggle" href="#"  role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><b-icon icon="box-seam"></b-icon> {{ $t('product') }}</a>
              <div class="dropdown-menu dropdown-menu-left" aria-labelledby="product">
                <router-link v-if="checkRoles('product_details_View')" :to="{ name: 'product.ProductList' }" class="dropdown-item pl-3">                
                  <fa icon="utensils" fixed-width/>
                  {{ $t('product_list') }}
                </router-link>
                <router-link v-if="checkRoles('product_details_View')" :to="{ name: 'product.ProductParts' }" class="dropdown-item pl-3">
                  <b-icon icon="columns-gap"></b-icon>
                  {{ $t('product_parts') }}
                </router-link>
                <router-link v-if="checkRoles('po_list_View')" :to="{ name: 'po.PoList' }" class="dropdown-item pl-3">                
                  <b-icon icon="alarm"></b-icon>
                  {{ $t('po_list') }}
                </router-link>
                <router-link v-if="checkRoles('monitor_etd_View')" :to="{ name: 'po.EtdMonitor' }" class="dropdown-item pl-3">
                  <b-icon icon="alarm-fill"></b-icon>
                  {{ $t('monitor_etd') }}
                </router-link>
                <!-- <router-link v-if="checkRoles('InventoryItem_View')" :to="{ name: 'product.RequisitionList' }" class="dropdown-item pl-3">
                  <b-icon icon="basket2-fill"></b-icon>
                  {{ $t('requisition') }}
                </router-link> -->
              </div>
            </li>
            <li v-if="user  && module_no == 1 && (checkRoles('production_View'))" class="nav-item dropdown">
              <a id="wip" class="nav-link dropdown-toggle" href="#"  role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><b-icon icon="tools"></b-icon> {{ $t('production') }}</a>
              <div class="dropdown-menu dropdown-menu-left" aria-labelledby="product">
                <!-- <router-link v-if="checkRoles('production_View')" :to="{ name: 'production.ProductionMonitor' }" class="dropdown-item pl-3">                
                  <b-icon icon="list-check"></b-icon>
                  {{ $t('production_monitor') }}
                </router-link> -->
                <router-link v-if="checkRoles('production_Insert')" :to="{ name: 'production.HourlyProduction' }" class="dropdown-item pl-3">
                  <b-icon icon="bricks"></b-icon>
                  {{ $t('hourly_production') }}
                </router-link>
                <router-link v-if="checkRoles('production_View')" :to="{ name: 'production.DailyProduction' }" class="dropdown-item pl-3">
                  <b-icon icon="graph-up"></b-icon>
                  {{ $t('daily_production') }}
                </router-link>
                <router-link v-if="checkRoles('production_Insert')" :to="{ name: 'production.PartsProduction' }" class="dropdown-item pl-3">
                  <b-icon icon="columns-gap"></b-icon>
                  {{ $t('parts_production') }}
                </router-link>
                <router-link v-if="checkRoles('production_View')" :to="{ name: 'production.PartsProductionReport' }" class="dropdown-item pl-3">
                  <b-icon icon="columns"></b-icon>
                  {{ $t('parts_production_report') }}
                </router-link>
                <router-link v-if="checkRoles('production_View')" :to="{ name: 'production.MonitorPartsProduction' }" class="dropdown-item pl-3">
                  <b-iconstack>
                    <b-icon stacked icon="columns" scale="0.5" shift-h="-1" shift-v="1"></b-icon>
                    <b-icon stacked icon="search" scale="1.15"></b-icon>
                  </b-iconstack>
                  {{ $t('monitor_parts_production') }}
                </router-link>
                <!-- <router-link v-if="checkRoles('production_View')" :to="{ name: 'production.ProductionPlan' }" class="dropdown-item pl-3">
                  <b-icon icon="calculator"></b-icon>
                  {{ $t('production_plan') }}
                </router-link> -->
              </div>
            </li>
            <li v-if="user && module_no == 2 && (checkRoles('employee_profile_View') || checkRoles('personnel_file_View') || checkRoles('increment_file_View'))" class="nav-item dropdown">
              <a id="employee" class="nav-link dropdown-toggle" href="#"  role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><b-icon icon="people-fill"></b-icon> {{ $t('employee_management') }}</a>
              <div class="dropdown-menu dropdown-menu-left" aria-labelledby="employee">
                <router-link v-if="checkRoles('employee_profile_View')" :to="{ name: 'hr.EmployeeProfile' }" class="dropdown-item pl-3">                
                  <b-icon icon="person-lines-fill"></b-icon>
                  {{ $t('employee_profile') }}
                </router-link>
                <router-link v-if="checkRoles('personnel_file_View')" :to="{ name: 'hr.PersonnelFile' }" class="dropdown-item pl-3">                
                  <b-icon icon="briefcase-fill"></b-icon>
                  {{ $t('personnel_file') }}
                </router-link>
                <router-link v-if="checkRoles('increment_file_View')" :to="{ name: 'hr.IncrementFile' }" class="dropdown-item pl-3">                
                  <b-icon icon="journal-arrow-up"></b-icon>
                  {{ $t('increment_file') }}
                </router-link>
                <router-link v-if="checkRoles('employee_profile_View')" :to="{ name: 'hr.EmployeeExit' }" class="dropdown-item pl-3">                
                  <b-icon icon="person-x-fill"></b-icon>
                  {{ $t('employee_exit') }}
                </router-link>
              </div>
            </li>
            <li v-if="user && module_no == 2 && (checkRoles('upload_attendance_View') || checkRoles('holiday_management_View') || checkRoles('leave_management_View'))" class="nav-item dropdown">
              <a id="leave" class="nav-link dropdown-toggle" href="#"  role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><b-icon icon="smartwatch"></b-icon> {{ $t('attendance') }}</a>
              <div class="dropdown-menu dropdown-menu-left" aria-labelledby="leave">
                <router-link v-if="checkRoles('upload_attendance_View')" :to="{ name: 'hr.UploadAttendance' }" class="dropdown-item pl-3">                
                  <b-icon icon="cloud-upload-fill"></b-icon>
                  {{ $t('upload_attendance') }}
                </router-link>
                <router-link v-if="checkRoles('upload_attendance_View')" :to="{ name: 'hr.DailyAttendance' }" class="dropdown-item pl-3">                
                  <b-icon icon="journal-text"></b-icon>
                  {{ $t('daily_attendance') }}
                </router-link>
                <router-link v-if="checkRoles('upload_attendance_View')" :to="{ name: 'hr.JobCard' }" class="dropdown-item pl-3">                
                  <b-icon icon="file-earmark-medical"></b-icon>
                  {{ $t('job_card') }}
                </router-link>
                <router-link v-if="checkRoles('leave_management_View')" :to="{ name: 'hr.LeaveManagement' }" class="dropdown-item pl-3">                
                  <b-icon icon="calendar-x-fill"></b-icon>
                  {{ $t('leave_management') }}
                </router-link>
                <router-link v-if="checkRoles('holiday_management_View')" :to="{ name: 'hr.HolidayManagement' }" class="dropdown-item pl-3">                
                  <b-icon icon="calendar-day-fill"></b-icon>
                  {{ $t('holiday_management') }}
                </router-link>
              </div>
            </li>
            <li v-if="user && module_no == 2 && (checkRoles('salary_sheet_View') || checkRoles('salary_management_View'))" class="nav-item dropdown">
              <a id="payroll" class="nav-link dropdown-toggle" href="#"  role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><fa icon="money-check-alt" fixed-width /> {{ $t('payroll') }}</a>
              <div class="dropdown-menu dropdown-menu-left" aria-labelledby="paroll">
                <router-link v-if="checkRoles('salary_management_View')" :to="{ name: 'hr.SalaryManagement' }" class="dropdown-item pl-3">                
                  <fa icon="money-bill-alt" fixed-width /> 
                  {{ $t('salary_management') }}
                </router-link>
                <router-link v-if="checkRoles('salary_sheet_View')" :to="{ name: 'hr.SalarySheet' }" class="dropdown-item pl-3">                
                  <fa icon="file-invoice-dollar" fixed-width />
                  {{ $t('salary_sheet') }}
                </router-link>
                <router-link v-if="checkRoles('salary_sheet_View')" :to="{ name: 'hr.PaySlip' }" class="dropdown-item pl-3">                
                  <b-icon icon="credit-card2-front-fill"></b-icon>
                  {{ $t('pay_slip') }}
                </router-link>
                <router-link v-if="checkRoles('salary_sheet_View')" :to="{ name: 'hr.ProvidantFund' }" class="dropdown-item pl-3">                
                  <fa icon="hand-holding-usd" fixed-width />
                  {{ $t('providant_fund') }}
                </router-link>
              </div>
            </li>
          </ul>

          <ul class="navbar-nav ml-auto">
            <!-- Authenticated -->
            <li v-if="user" class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-dark"
                href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
              >
                <img :src="user.photo_url" class="rounded-circle profile-photo mr-1">
                {{ user.name }}
              </a>
              <div class="dropdown-menu">
                <router-link :to="{ name: 'settings.profile' }" class="dropdown-item pl-3">
                  <fa icon="cog" fixed-width />
                  {{ $t('settings') }}
                </router-link> 

                <div class="dropdown-divider" />
                <a href="#" class="dropdown-item pl-3" @click.prevent="logout">
                  <fa icon="sign-out-alt" fixed-width />
                  {{ $t('logout') }}
                </a>
              </div>
            </li>
            <!-- Guest -->
            <template v-else>
              <li class="nav-item">
                <router-link :to="{ name: 'login' }" class="nav-link" active-class="active">
                  {{ $t('login') }}
                </router-link>
              </li>
              <!-- <li class="nav-item">
                <router-link :to="{ name: 'register' }" class="nav-link" active-class="active">
                  {{ $t('register') }}
                </router-link>
              </li> -->
            </template>
          </ul>
        </div>
      </div>
    </nav>
    <!-- Start Payment Details Modal -->
    <b-modal ref="payment" id="payment" size="lg" title="Your payment is due" no-close-on-backdrop>                        
        <div class="modal-body row m-0 p-0 mb-2 text-center">
          <h3 class="mx-auto my-3">Please pay your monthly subscription fee ৳ 3,000 to stay connected</h3>
          <h1 id="timer_id" class="mx-auto my-5 bg-info rounded-pill px-5 py-3 text-white"></h1>
        </div>
        <template v-slot:modal-footer>
            <button @click="payNow" class="mdb btn btn-outline-default">Pay Now</button>
            <button @click="$refs['payment'].hide()" type="button" class="mdb btn btn-outline-mdb-color">{{$t('Close')}}</button>
        </template>
    </b-modal>                    
    <!-- End Payment Details Modal -->
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import LocaleDropdown from './LocaleDropdown'
import Cookies from 'js-cookie'

export default {
  components: {
    LocaleDropdown
  },

  data: () => ({
    appName: window.config.appName,
  }),

  created() {
    if (this.user) this.$store.dispatch('role/fetchRoles')
  },

  mounted() {
    this.$store.dispatch('role/chooseModule')

    // Start for paument check-----------------
    // let payment = [], countDays = new Date()

    // fetch(`api/payment`)
    // .then(res => res.json())
    // .then(res => {
    //   payment = res['payment'];
    //   if (payment.length == 0) {
    //     this.CountDownTimer()
    //     this.$refs['payment'].show()
    //     if(countDays.getDate()>5) {
    //       Cookies.set('payment', 0, { expires: 365 })
    //       if (this.user) { 
    //         // Log out the user.
    //         this.$store.dispatch('auth/logout')

    //         // Redirect to login.
    //         this.$router.push({ name: 'login' })
    //       }
    //     }
    //   }
      
    //   if (payment.length > 0 || countDays.getDate()<6) {        
    //     Cookies.set('payment', 1, { expires: 365 })
    //   }
    // })
    // End for paument check-----------------

  },

  computed: {
    ...mapGetters({
      user: 'auth/user',
      module_no: 'role/module_no',
      roles: 'role/roles'
    }),

    // module_no() {
    //   // return Cookies.get('module_no')
    //   return localStorage.getItem("module_no")
    // },
  },

  methods: {
    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to login.
      this.$router.push({ name: 'login' })
    },

    checkRoles(role) {
        for (let i = 0; i < this.roles.length; i++) {
            if (this.roles[i]['name'] == role) {
                return true
            }                
        } return false
    },

    CountDownTimer() {     

      var date = new Date(),
          year = date.getFullYear(),
          mnth = ("0" + (date.getMonth() + 1)).slice(-2),
          day = '06'
          
      var end = new Date([year, mnth, day].join("-"));
      var _second = 1000;
      var _minute = _second * 60;
      var _hour = _minute * 60;
      var _day = _hour * 24;
      var timer;

      function showRemaining() {
            var now = new Date();
            var distance = end - now - 21600000;
            if (distance < 0) {
                clearInterval(timer);
                document.getElementById("timer_id").innerHTML = 'EXPIRED!';
                return
            }
            var days = Math.floor(distance / _day);
            var hours = Math.floor((distance % _day) / _hour);
            var minutes = Math.floor((distance % _hour) / _minute);
            var seconds = Math.floor((distance % _minute) / _second);

            if (document.getElementById("timer_id")) {              
              document.getElementById("timer_id").innerHTML = days + 'days ';
              document.getElementById("timer_id").innerHTML = days + 'days ';
              document.getElementById("timer_id").innerHTML += hours + 'hrs ';
              document.getElementById("timer_id").innerHTML += minutes + 'mins ';
              document.getElementById("timer_id").innerHTML += seconds + 'secs';
            }
        }
      timer = setInterval(showRemaining, 1000);
    },

    payNow() {
      this.$refs['payment'].hide()
      this.$router.push({ name: 'etc.Payment' })
    },
  }
}
</script>

<style scoped>
.profile-photo {
  width: 2rem;
  height: 2rem;
  margin: -.375rem 0;
}
</style>
