<template>
  <div class="header row">
    <div class="logo col-xs-12 col-lg-3">
      <nuxt-link :to="'/'">
        <img src="https://altertravel.ru/i/logo.png" alt="Альтернативный путеводитель">
      </nuxt-link>
    </div>
    <Menu />
  </div>
</template>

<script>
import Menu from '~/components/Menu';

export default {
  components: { Menu },
}

</script>

<style scoped>
  .header {
    padding: 10px 0;
  }
  .row {
    background: #ebf1f5;
  }
</style>
