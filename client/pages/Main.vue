<template>
  <div class="container page">
    <div class="row">
      <div class="col-sm-12">
        <h1>
          Карта Альтернативного Путеводителя
        </h1>
      </div>
    </div>
    <Map :center="center" :types="types" />
    <div class="row">
      <div class="col-sm-12">
        <h2>Новые публикации</h2>
      </div>
    </div>
    <Gallery :objects="pois_new" :loading="loadingPoisNew" />
    <div class="row">
      <div class="col-sm-12">
        <h2>Последние комментарии</h2>
      </div>
    </div>
    <CommentsCarousel />
    <div class="row">
      <div class="col-sm-12">
        <h2>Популярные в {{ mesyats }} публикации</h2>
      </div>
    </div>
    <Gallery :objects="pois_popular" :loading="loadingPoisPopular" />
    <div class="row">
      <div v-if="countries.length" class="col-sm-4">
        <h2>Страны</h2>
        <ul>
          <li
            v-for="region in countries"
            :key="region.id"
          >
            <nuxt-link :to="region.url">
              <img v-if="region.flag" width="16" height="16" :src="`https://altertravel.ru/i/flags/` + region.flag" alt="flag">
              {{ region.name }} ({{ region.count }})
            </nuxt-link>
          </li>
        </ul>
      </div>
      <div v-if="tags.length" class="col-sm-4">
        <h2>Метки</h2>
        <ul>
          <li
            v-for="region in tags"
            :key="region.id"
          >
            <nuxt-link :to="region.url">
              <img v-if="region.flag" width="16" height="16" :src="`https://altertravel.ru/i/flags/` + region.flag" alt="flag">
              {{ region.name }} ({{ region.count }})
            </nuxt-link>
          </li>
        </ul>
      </div>
      <div class="col-sm-4">
      </div>
    </div>
  </div>
</template>
<script>
import axios from 'axios'

export default {
  data () {
    return {
      pois_new: [],
      loadingPoisNew: true,
      pois_popular: [],
      loadingPoisPopular: true,
      center: { lat: null, lng: null },
      types: [],
      m_names: ['январе', 'феврале', 'марте', 
               'апреле', 'мае', 'июне', 'июле', 
               'августе', 'сентябре', 'октябре', 'ноябре', 'декабре'],
    }
  },
  async fetch () {
    await this.fetchPoisNew()
    await this.fetchPoisPopular()
  },
  head: {
    title: 'Карта достопримечательностей для самостоятельных путешественников',
    meta: [
      {
        name: 'description',
        content: 'Каталог достопримечательностей на карте. Для самостоятельной организации путешествия!'
      }
    ]
  },
  computed: {
    mesyats: {
      get: function() {
        let d = new Date()
        return this.m_names[d.getMonth()]
      }
    },
    countries: {
      get: function() {
        return { ...this.$store.state.countries }
      }
    },
    tags: {
      get: function() {
        return { ...this.$store.state.tags }
      }
    }
  },
  created () {
  },
  mounted () {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          this.center = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          }
          this.mylocation = this.center
        },
        () => {
        }
      )
    }
    this.fetchPoisNew()
    this.fetchPoisPopular()
  },
  methods: {
    async fetchPoisNew () {
      this.loadingPoisNew = true
      const { data } = await axios.get('/pois', { params: {limit: 4, sort: 'date', direction: 'asc'} })
      this.pois_new = data.data
      this.loadingPoisNew = false
    },
    async fetchPoisPopular () {
      this.loadingPoisPopular = true
      const { data } = await axios.get('/pois', { params: {limit: 4, sort: 'views_month', direction: 'asc'} })
      this.pois_popular = data.data
      this.loadingPoisPopular = false
    }
  }
}
</script>

<style>

  h1.view {
      padding-left: 35px;
      cursor: pointer;
  }
</style>
