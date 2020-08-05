<template>
  <div class="flex-center position-ref full-height">
    <div class="content">
        <div class="title m-b-md">
            {{ $t('appName') }}
        </div>

        <div class="links">
          <router-link :to="{ name: 'login' }">
            {{ $t('software_title') }}
          </router-link>
        </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import LocaleDropdown from '../components/LocaleDropdown'

export default {
  // layout: 'basic',

  metaInfo () {
    return { title: this.$t('home') }
  },

  data: () => ({
    title: window.config.appName
  }),

  computed: mapGetters({
    authenticated: 'auth/check'
  }),

  components: {
    LocaleDropdown
  },
}
</script>

<style lang="scss" scoped>
html, body {
    color: #636b6f;
    font-family: 'Nunito', sans-serif;
    font-weight: 200;
    height: 100vh;
    margin: 0;
}

.full-height {
    height: 80vh;
}

.flex-center {
    align-items: center;
    display: flex;
    justify-content: center;
}

.position-ref {
    position: relative;
}

.top-right {
    position: absolute;
    right: 10px;
    top: 18px;
}

.content {
    text-align: center;
}

.title {
    font-size: 84px;
}

.links > a {
    color: #636b6f;
    padding: 0 25px;
    font-size: 20px;
    font-weight: 600;
    letter-spacing: .1rem;
    text-decoration: none;
    text-transform: uppercase;
}

.m-b-md {
    margin-bottom: 30px;
}
</style>
