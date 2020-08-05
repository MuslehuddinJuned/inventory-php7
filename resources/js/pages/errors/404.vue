<template>
  <div class="flex-center position-ref full-height">
    <div class="content">
      <div class="title">
        <h3 class="display-3 text-uppercase text-white"> {{ $t('page_not_found') }} </h3>
      </div>
      <div class="links text-black-50">
      <router-link :to="{ name: 'welcome' }">
        {{ $t('go_home') }}
      </router-link>
    </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFound'
}
</script>
<style lang="scss" scoped>
html, body {
    color: #636b6f !important;
    font-family: 'Raleway', sans-serif !important;
    font-weight: 100;
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

  .content {
    text-align: center;
  }

  .links > a {
    color: #636b6f;
    padding: 0 25px;
    font-size: 24px;
    font-weight: 600;
    letter-spacing: .1rem;
    text-decoration: none;
    text-transform: uppercase;
}
</style>
