<template>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h3>{{ $t('choose_a_module') }}</h3>
            </div>
            <div class="container card-body row m-0 p-4">
                <div v-if="checkRoles('inventory_mgt_View')" class="col-md-6 text-center mx-auto"><b-img @click="chooseModule(1)" thumbnail fluid src="https://i.pinimg.com/originals/52/a0/8c/52a08c0ca3814e07d15932a2f2b900fa.jpg" :alt="$t('inventory_mgt')" style="cursor: pointer;"></b-img></div>
                <div v-if="checkRoles('hrm_View')" class="col-md-6 text-center mx-auto"><b-img @click="chooseModule(2)" thumbnail fluid src="https://i.pinimg.com/originals/db/47/87/db4787c8e7fbf41afafaa33d3883bf52.jpg" :alt="$t('hrm')" style="cursor: pointer;"></b-img></div>
                <div v-if="!checkRoles('hrm_View') && !checkRoles('inventory_mgt_View')" class="col-md-6 text-center mx-auto"><h2>Please contact Admin for permission</h2></div>
            </div>
        </div>
    </div>
  
</template>

<script>
import Cookies from 'js-cookie'
export default {
  middleware: 'auth',

  metaInfo () {
    return { title: this.$t('home') }
  },

  data() {
    return{
      roles: [],
    }
  },

  mounted() {
    fetch(`api/settings/roles`)
        .then(res => res.json())
        .then(res => {
            this.roles = res['allRoles'];
        })
  },

  methods: {
    chooseModule(id) {
      // Cookies.set('module_no', id, { expires: 365 })
      localStorage.setItem("module_no" , id)
      location.reload()
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
