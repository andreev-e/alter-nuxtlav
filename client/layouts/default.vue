<template>
  <div>
    <div class="container page">
      <Header />
    </div>
      <nuxt />
    <div class="container page">
      <Footer />
    </div>
  </div>
</template>

<script>
import Header from '~/components/Header';
import Footer from '~/components/Footer';

export default {
  components: { Header, Footer },
  computed: {
    h1() {
      return this.$store.getters['page/getH1']
    },
    breadcrumbs() {
      return this.$store.getters['page/getBreadcrumbs']
    }
  }
}

</script>
