<template>
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle my-auto" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <fa icon="language"/>
       <!-- {{ locales[locale] }} -->
    </a>
    <div class="dropdown-menu">
     <a v-for="(value, key) in locales" :key="key" v-if="value != 'ES'" class="dropdown-item" href="#"
         @click.prevent="setLocale(key)"
      >
      <span v-if="value == 'EN'"><fa icon="user-secret" fixed-width/> {{ value }}</span>   
      <span v-if="value == '中文'"><fa icon="user-ninja" fixed-width/> {{ value }}</span>   
      </a>
    </div>
  </li>
</template>

<script>
import { mapGetters } from 'vuex'
import { loadMessages } from '~/plugins/i18n'

export default {
  computed: mapGetters({
    locale: 'lang/locale',
    locales: 'lang/locales'
  }),  

  methods: {
    setLocale (locale) {
      if (this.$i18n.locale !== locale) {
        loadMessages(locale)

        this.$store.dispatch('lang/setLocale', { locale })
      }
    }
  }
}
</script>
