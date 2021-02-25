<template>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h3 v-if="isBusy"><b-icon icon="circle-fill" animation="throb"></b-icon> Please wait ...</h3>
                <h3 v-else>{{ $t('choose_a_module') }}</h3>
            </div>
            <div v-if="!isBusy" class="container card-body row m-0 p-4">
                <div v-if="checkRoles('inventory_mgt_View')" class="col-md-6 text-center mx-auto"><b-img @click="chooseModule(1)" thumbnail fluid src="https://i.pinimg.com/originals/52/a0/8c/52a08c0ca3814e07d15932a2f2b900fa.jpg" :alt="$t('inventory_mgt')" style="cursor: pointer;"></b-img></div>
                <div v-if="checkRoles('hrm_View')" class="col-md-6 text-center mx-auto"><b-img @click="chooseModule(2)" thumbnail fluid src="https://i.pinimg.com/originals/db/47/87/db4787c8e7fbf41afafaa33d3883bf52.jpg" :alt="$t('hrm')" style="cursor: pointer;"></b-img></div>
                <div v-if="!checkRoles('hrm_View') && !checkRoles('inventory_mgt_View')" class="col-md-6 text-center mx-auto"><h2>Please contact Admin for permission</h2></div>
            </div>
        </div>
    </div>
  
</template>

<script>
import Cookies from 'js-cookie'
import { mapGetters } from 'vuex';
export default {
  middleware: 'auth',

  metaInfo () {
    return { title: this.$t('home') }
  },

  data() {
    return{
      isBusy: false,
    }
  },

  created() {
      this.$store.dispatch('role/chooseModule')
      this.$store.dispatch('role/fetchRoles')
  },

  methods: {
    chooseModule(id) {
      // Cookies.set('module_no', id, { expires: 365 })
      localStorage.setItem("module_no" , id)
      this.$store.dispatch('role/chooseModule')
      // this.$router.push({ name: 'dashboard' })
    },

    checkRoles(role) {
        for (let i = 0; i < this.roles.length; i++) {
            if (this.roles[i]['name'] == role) {
                return true
            }                
        } return false
    },
  },

  computed: {
      ...mapGetters({
          module_no: 'role/module_no',
          roles: 'role/roles'
      }),
  },
}
</script>
