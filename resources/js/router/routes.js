function page (path) {
  return () => import(/* webpackChunkName: '' */ `~/pages/${path}`).then(m => m.default || m)
}

export default [
  { path: '/', name: 'welcome', component: page('welcome.vue') },

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
  { path: '/requisitionbyproduct', name: 'product.RequisitionList', component: page('product/RequisitionList.vue') },
  { path: '/semifinished', name: 'wip.SemifinishedGoods', component: page('wip/SemifinishedGoods.vue') },
  { path: '/finished', name: 'wip.FinishedGoods', component: page('wip/FinishedGoods.vue') },
  { path: '/goodslocation', name: 'wip.GoodsLocation', component: page('wip/GoodsLocation.vue') },

  { path: '/home', name: 'home', component: page('home.vue') },
  { path: '/settings',
    component: page('settings/index.vue'),
    children: [
      { path: '', redirect: { name: 'settings.profile' } },
      { path: 'profile', name: 'settings.profile', component: page('settings/profile.vue') },
      { path: 'password', name: 'settings.password', component: page('settings/password.vue') }
    ] },

  { path: '*', component: page('errors/404.vue') }
]
