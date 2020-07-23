<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
      <router-link :to="{ name: user ? 'home' : 'welcome' }" class="navbar-brand">
        {{ $t('appName') }}
      </router-link>

      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false">
        <span class="navbar-toggler-icon" />
      </button>

      <div id="navbarToggler" class="collapse navbar-collapse">
        <ul class="navbar-nav">
          <locale-dropdown />
          <li v-if="user" class="nav-item dropdown">
            <a id="inventory" class="nav-link dropdown-toggle" href="#"  role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><b-icon icon="cart4"></b-icon> {{ $t('Inventory') }}</a>
            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="inventory">
              <router-link :to="{ name: 'inventory.InventoryList' }" class="dropdown-item pl-3">                
                <b-icon icon="cart-check"></b-icon>
                {{ $t('InventoryItem') }}
              </router-link>
              <router-link :to="{ name: 'inventory.InventoryReceive' }" class="dropdown-item pl-3">
                <b-icon icon="cart-plus"></b-icon>
                {{ $t('ItemReceive') }}
              </router-link>
              <router-link :to="{ name: 'inventory.InventoryIssue' }" class="dropdown-item pl-3">
                <b-icon icon="cart-dash"></b-icon>
                {{ $t('ItemIssue') }}
              </router-link>
              <router-link :to="{ name: 'inventory.RequisitionList' }" class="dropdown-item pl-3">
                <b-icon icon="basket2-fill"></b-icon>
                {{ $t('requisition') }}
              </router-link>
            </div>
          </li>
          <li v-if="user" class="nav-item dropdown">
            <a id="product" class="nav-link dropdown-toggle" href="#"  role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><b-icon icon="box-seam"></b-icon> {{ $t('product') }}</a>
            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="product">
              <router-link :to="{ name: 'product.ProductList' }" class="dropdown-item pl-3">                
                <fa icon="utensils" fixed-width/>
                {{ $t('product_list') }}
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

export default {
  components: {
    LocaleDropdown
  },

  data: () => ({
    appName: window.config.appName
  }),

  computed: mapGetters({
    user: 'auth/user'
  }),

  methods: {
    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to login.
      this.$router.push({ name: 'login' })
    }
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
