<template>
  <div class="header-menu col-sm-9">
    <ul>
      <li class="region_select">
        <a href="#">
          Регион
          <span class="subtitle d-none d-md-block">выбрать</span>
        </a>
        <ul>
          <li
            v-for="region in regions"
            :key="region.id"
          >
            <nuxt-link :to="region.url">
              <img v-if="region.flag" width="16" height="16" :src="`https://altertravel-photoes.s3.eu-central-1.amazonaws.com/i/flags/${region.flag}`" alt="flag">
              {{ region.name }}
            </nuxt-link>
          </li>
        </ul>
      </li>
      <li class="region_select">
        <a href="#">
          Теги
          <span class="subtitle d-none d-md-block">выбрать</span>
        </a>
        <ul>
          <li
            v-for="tag in tags"
            :key="tag.id"
          >
            <nuxt-link :to="tag.url">
              {{ tag.name }} ({{ tag.count }})
            </nuxt-link>
          </li>
        </ul>
      </li>
      <li>
        <router-link :to="{ name: 'routes' }">
          Маршруты
          <span class="subtitle d-none d-md-block">готовые треки</span>
        </router-link>
      </li>
      <li>
        <router-link :to="{ name: 'izbrannoe' }">
          <b-icon icon="star-fill" aria-hidden="true" variant="warning" />
          <span class="d-none d-md-inline">Избранное </span>
          {{ countchosen }}
          <span class="subtitle d-none d-md-block">Строим маршрут с точками</span>
        </router-link>
      </li>
      <li v-if="authenticated">
        <router-link :to="{ name: 'secure' }">
          <b-img
            :src="`https://altertravel.ru/authors/${user.olduser.username}.jpg`"
            rounded="circle"
            alt="ava"
            height="19"
          />
          <span class="d-none d-md-inline">{{ user.name }}</span>
          <span class="subtitle d-none d-md-block">точки и маршруты</span>
        </router-link>
      </li>
    </ul>
  </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'

export default {
  name: 'Menu',
  components: {},
  data () {
    return {
      tags: [],
      regions: [],
    }
  },
  computed: {
    countchosen: {
      get: function () {
        if(process.client) {
          return this.localStorage.chosen ? this.localStorage.chosen.length : '';
        } else {
          return '';
        }
      }
    },
    ...mapGetters({
      authenticated: 'auth/check',
      user: 'auth/user'
    }),
  },
  async fetch () {
    await this.fetchTags()
    await this.fetchCountries()
  },
  methods: {
    async fetchCountries () {
      let { data } = await axios.get('/countries')
      this.regions = data.data
      this.$store.state.countries = { ...this.regions }
    },
    async fetchTags () {
      let { data } = await axios.get('/tags')
      this.tags = data.data
      this.$store.state.tags = { ...this.tags }
    },
  }
}
</script>

<style scoped>
  .header-menu {
    color: #fff;
    font-size: 20px;
    margin-top: 30px;
    line-height: 22px;
  }
  .header-menu li:hover {
    background-color: rgba(255, 255, 255, .90);
    border: 0px solid;
    box-shadow: 1 0 1px rgb(0, 0, 0);
    box-shadow: inset -2 0 1px rgb(255, 255, 255);
  }
  .header-menu ul li {
    text-align: center;
    float: left;
    margin: 0;
    background-color: #A2B9C8;
    border-top-left-radius: 9px;
    border-top-right-radius: 9px;
    padding: 4px 4px;
    margin-left: 1px;
  }
  .region_select ul {
    display: none;
    position: absolute;
    z-index: 999;
    box-shadow: 10px 72px 72px -10px rgba(0,0,0,0.3);
    width: 350px;
    margin: 0;
    margin-top: -1px;
    margin-left: -5px;
    background: #FFF;
    color: #244255;
    border-bottom-left-radius: 6px;
    border-bottom-right-radius: 6px;
    border: 1px solid #cbd6ee;
    border-top: none;
  }
  .header-menu a {
    color: #244255;
    text-decoration: none;
    list-style:none;
    font-size: 20px;
  }
  .region_select:hover ul {
    display: block;
  }
  .region_select ul li {
    margin: 0;
    font-size: 14px;
    padding: 0;
    float: none;
    text-align: left;
    line-height: 24px;
    background: none;
    height: 26px;
    text-align: left;
    padding-left: 4px;
  }
  .header-menu ul li.region_select ul li {
    height: 26px;
    text-align: left;
    padding-left: 4px;
  }
  .region_select a {
    margin: 0;
    padding: 0;
    overflow: hidden;
  }
    ul, li {
    list-style-type: none;
  }
  .header-menu ul li a span.subtitle {
    font-size: 14px;
    display: block;
  }
  .region_select img {
    margin-bottom: -4px;
    margin-right: 5px;
  }
</style>
