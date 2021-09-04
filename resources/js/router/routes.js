function page (path) {
  return () => import(/* webpackChunkName: '' */ `~/pages/${path}`).then(m => m.default || m)
}

export default [
  { path: '/', name: 'welcome', component: page('welcome.vue') },
  { path: '/dashboard', name: 'dashboard', component: page('dashboard.vue') },

  { path: '/login', name: 'login', component: page('auth/login.vue') },
  { path: '/register', name: 'register', component: page('auth/register.vue') },
  { path: '/password/reset', name: 'password.request', component: page('auth/password/email.vue') },
  { path: '/password/reset/:token', name: 'password.reset', component: page('auth/password/reset.vue') },
  { path: '/email/verify/:id', name: 'verification.verify', component: page('auth/verification/verify.vue') },
  { path: '/email/resend', name: 'verification.resend', component: page('auth/verification/resend.vue') },


  { path: '/inventory', name: 'inventory.InventoryList', component: page('inventory/InventoryList.vue') },
  { path: '/inventoryetd', name: 'inventory.InventoryListEtd', component: page('inventory/InventoryListEtd.vue') },
  { path: '/inventoryreceive', name: 'inventory.InventoryReceive', component: page('inventory/InventoryReceive.vue') },
  { path: '/inventoryissue', name: 'inventory.InventoryIssue', component: page('inventory/InventoryIssue.vue') },
  { path: '/requisition', name: 'inventory.RequisitionList', component: page('inventory/RequisitionList.vue') },
  { path: '/issuearchive', name: 'inventory.InvIssArchive', component: page('inventory/InvIssArchive.vue') },
  { path: '/balancesheet', name: 'inventory.BalanceSheet', component: page('inventory/BalanceSheet.vue') },
  { path: '/product', name: 'product.ProductList', component: page('product/ProductList.vue') },
  { path: '/product-parts', name: 'product.ProductParts', component: page('product/ProductParts.vue') },
  { path: '/requisitionbyproduct', name: 'product.RequisitionList', component: page('product/RequisitionList.vue') },
  // { path: '/semifinished', name: 'wip.SemifinishedGoods', component: page('wip/SemifinishedGoods.vue') },
  // { path: '/finished', name: 'wip.FinishedGoods', component: page('wip/FinishedGoods.vue') },
  // { path: '/goodslocation', name: 'wip.GoodsLocation', component: page('wip/GoodsLocation.vue') },
  { path: '/polist', name: 'po.PoList', component: page('po/PoList.vue') },
  { path: '/etdmonitor', name: 'po.EtdMonitor', component: page('po/EtdMonitor.vue') },
  { path: '/daily-production', name: 'production.DailyProduction', component: page('production/DailyProduction.vue') },
  { path: '/hourly-production', name: 'production.HourlyProduction', component: page('production/HourlyProduction.vue') },
  { path: '/parts-production', name: 'production.PartsProduction', component: page('production/PartsProduction.vue') },
  { path: '/parts-production-report', name: 'production.PartsProductionReport', component: page('production/PartsProductionReport.vue') },
  { path: '/production-monitor', name: 'production.ProductionMonitor', component: page('production/ProductionMonitor.vue') },
  { path: '/production-plan', name: 'production.ProductionPlan', component: page('production/ProductionPlan.vue') },
  { path: '/monitor-parts-production', name: 'production.MonitorPartsProduction', component: page('production/MonitorPartsProduction.vue') },


  { path: '/employee-profile', name: 'hr.EmployeeProfile', component: page('hr/profile/EmployeeProfile.vue') },
  { path: '/employee-exit', name: 'hr.EmployeeExit', component: page('hr/profile/EmployeeExit.vue') },
  { path: '/personnel-file', name: 'hr.PersonnelFile', component: page('hr/profile/PersonnelFile.vue') },
  { path: '/increment-file', name: 'hr.IncrementFile', component: page('hr/profile/IncrementFile.vue') },
  { path: '/holiday-management', name: 'hr.HolidayManagement', component: page('hr/attendance/HolidayManagement.vue') },
  { path: '/leave-management', name: 'hr.LeaveManagement', component: page('hr/attendance/LeaveManagement.vue') },
  { path: '/upload-attendance', name: 'hr.UploadAttendance', component: page('hr/attendance/UploadAttendance.vue') },
  { path: '/daily-attendance', name: 'hr.DailyAttendance', component: page('hr/attendance/DailyAttendance.vue') },
  { path: '/job-card', name: 'hr.JobCard', component: page('hr/attendance/JobCard.vue') },
  { path: '/salary-management', name: 'hr.SalaryManagement', component: page('hr/payroll/SalaryManagement.vue') },
  { path: '/salary-sheet', name: 'hr.SalarySheet', component: page('hr/payroll/SalarySheet.vue') },
  { path: '/pay-slip', name: 'hr.PaySlip', component: page('hr/payroll/PaySlip.vue') },
  { path: '/providant-fund', name: 'hr.ProvidantFund', component: page('hr/payroll/ProvidantFund.vue') },

  { path: '/payment', name: 'etc.Payment', component: page('etc/Payment.vue') },

  // Start Majesto
  { path: '/majesto-attendance', name: 'majesto.Attendance', component: page('majesto/Attendance.vue') },

  // End Majesto



  { path: '/role', name: 'settings.role', component: page('settings/role.vue') },
  { path: '/home', name: 'home', component: page('home.vue') },
  { path: '/settings',
    component: page('settings/index.vue'),
    children: [
      { path: '', redirect: { name: 'settings.profile' } },
      // { path: 'role', name: 'settings.role', component: page('settings/role.vue') },
      { path: 'profile', name: 'settings.profile', component: page('settings/profile.vue') },
      { path: 'password', name: 'settings.password', component: page('settings/password.vue') }
    ] },

  { path: '*', component: page('errors/404.vue') }
]
