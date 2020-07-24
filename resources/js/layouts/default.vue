<template>
  <div class="main-layout">
    <navbar />

    <div class="my-5">
      <child />
    </div>
    
    <div class="col-12 fixed-bottom d-flex align-items-center text-light" style="min-height: 35px; background-color: rgba(0,0,0,.7);">
        <!-- <span class="float-left ml-md-3">Sign in free to explore new horizon of Industrial Engineering</span>  -->
        <div class="mx-auto">
            <span class="mr-md-3 text-nowrap">© www.sustipe.com</span>
        </div>
    </div>
  </div>
</template>

<script>
import Navbar from '~/components/Navbar'

export default {
  name: 'MainLayout',

  components: {
    Navbar
  }
}
</script>
