<template>
  <div class="container page">
    <div class="row">
      <div class="col-sm-12">
        <h1 class="view">
          Личный кабинет автора
        </h1>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <b-tabs>
          <b-tab title="Ваши точки списком" active>
            <Gallery :objects="pois" :loading="loadingPois" />
            <b-pagination
              v-if="pages / perPage > 1"
              v-model="page"
              :total-rows="pages"
              :per-page="perPage"
              aria-controls="my-table"
            />
          </b-tab>
          <b-tab title="Ваши точки на карте">
            <Legend v-model="types" />
            <Map :my="1" :zoom="5" :types="types" :center="center"/>
          </b-tab>
          <b-tab title="Ваши маршруты">
            <Gallery :objects="routes" type="route" :loading="loadingRoutes" />
            <b-pagination
              v-if="pagesRoutes / perPageRoutes > 1"
              v-model="pageRoutes"
              :total-rows="pagesRoutes"
              :per-page="perPageRoutes"
              aria-controls="my-table"
            />
          </b-tab>
          <b-tab title="Избранное">
          </b-tab>
        </b-tabs>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  middleware: 'auth',
  data () {
    return {
      loadingPois: true,
      loadingRoutes: true,
      page: 1,
      pages: 0,
      perPage: 12,
      pageRoutes: 1,
      pagesRoutes: 0,
      perPageRoutes: 12,
      pois: [],
      routes: [],
      center: {},
      types: [],
    }
  },
  created () {
  },
  mounted () {
    this.fetchPois()
    this.fetchRoutes()
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
  },
  watch: {
    page: {
      handler (val) {
        this.fetchPois()
      }
    },
    pageRoutes: {
      handler (val) {
        this.fetchRoutes()
      }
    }
  },
  methods: {
    async fetchPois () {
      this.loadingPois = true
      const { data } = await axios.get('/pois', { params: { my: true, page: this.page, perPage: this.perPage } })
      this.pois = data.data
      this.pages = data.meta.total
      this.loadingPois = false
    },
    async fetchRoutes () {
      this.loading = true
      const { data } = await axios.get('/routes', { params: { my: true, page: this.pageRoutes, perPage: this.perPageRoutes } })
      this.routes = data.data
      this.pagesRoutes = data.meta.total
      this.loadingRoutes = false
    }
  }
}
</script>

<style>

</style>
