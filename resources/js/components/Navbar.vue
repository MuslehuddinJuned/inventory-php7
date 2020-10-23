<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container">
      <img class="img-responsive mr-2" src="/favicon.ico" width="30px" alt="SUSTipe">
      <router-link :to="{ name: user ? 'home' : 'welcome' }" class="navbar-brand">
        {{ $t('appName') }}
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
          <!-- <li v-if="user  && module_no == 1" class="nav-item dropdown">
            <a id="wip" class="nav-link dropdown-toggle" href="#"  role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><b-icon icon="hourglass-split"></b-icon> {{ $t('WIP') }}</a>
            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="product">
              <router-link v-if="checkRoles('InventoryItem_View')" :to="{ name: 'wip.SemifinishedGoods' }" class="dropdown-item pl-3">                
                <b-icon icon="hourglass-top"></b-icon>
                {{ $t('semifinished_goods') }}
              </router-link>
              <router-link v-if="checkRoles('InventoryItem_View')" :to="{ name: 'wip.FinishedGoods' }" class="dropdown-item pl-3">
                <b-icon icon="hourglass-bottom"></b-icon>
                {{ $t('finished_goods') }}
              </router-link>
              <router-link v-if="checkRoles('InventoryItem_View')" :to="{ name: 'wip.GoodsLocation' }" class="dropdown-item pl-3">
                <fa icon="map-marked-alt" fixed-width/>
                {{ $t('goods_location') }}
              </router-link>
            </div>
          </li> -->
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
            <li class="nav-item">
              <router-link :to="{ name: 'register' }" class="nav-link" active-class="active">
                {{ $t('register') }}
              </router-link>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>
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
    roles: [],
  }),

  mounted() {
    fetch(`api/settings/roles`)
    .then(res => res.json())
    .then(res => {
        this.roles = res['allRoles'];
    })
  },

  computed: {
    ...mapGetters({
      user: 'auth/user'
    }),

    module_no() {
      return Cookies.get('module_no')
    },
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
