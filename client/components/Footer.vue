<template>
  <div class="row footer">
    <div class="first_menu col-sm-4">
      <template v-if="authenticated">
        <router-link :to="{ name: 'secure' }">
          Кабинет автора
        </router-link>
        <router-link :to="{ name: 'settings' }">
          Настройки
        </router-link>
        <a href="#" @click.prevent="logout">
          Выйти
        </a>
      </template>
      <template v-else>
        <router-link :to="{ name: 'login' }">
          Вход для авторов
        </router-link>
        <router-link :to="{ name: 'register' }">
          Регистрация
        </router-link>
      </template>
    </div>
    <div class="copyright col-sm-4">
      2009 - {{ year }} © Альтернативный путеводитель
    </div>
    <div class="second_menu col-sm-4">
      <nuxt-link to="/user">
        Авторы
      </nuxt-link>
    </div>
    <div class="col-12">
      <Adsense
        data-ad-client="ca-pub-2550364618248551"
        data-ad-slot="3426016451"
        data-ad-format="auto"
      />
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'Footer',
  data () {
    return {
      year: 'year'
    }
  },
  created () {
    this.year = new Date().getFullYear()
  },
  computed: mapGetters({
    authenticated: 'auth/check'
  }),
  methods: {
    async logout () {
      await this.$store.dispatch('auth/logout')
      this.$router.push({ name: 'index' })
    }
  }
}
</script>

<style scoped>
.footer > * {
  padding: 20px;
}
</style>
