<template>
  <div class="main-layout">
    <div class="text-right col-12 onlyprint">{{Dated}}</div>
    <navbar />

    <div class="mt-2 mb-5">
      <child />
    </div>
    
    <!-- <div class="col-12 fixed-bottom d-flex align-items-center text-light" style="min-height: 35px; background-color: rgba(0,0,0,.7);">
        
        <div class="mx-auto">
            <span class="mr-md-3 text-nowrap">© www.sustipe.com</span>
        </div>
    </div> -->
  </div>
</template>

<script>
import Navbar from '~/components/Navbar'

export default {
  name: 'MainLayout',

  components: {
    Navbar
  },

  data() {
    return{
      Dated: new Date(),
    }
  }
}
</script>
