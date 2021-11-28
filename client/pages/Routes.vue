<template>
  <div class="container page">
    <div class="row">
      <div class="col-sm-12">
        <h1 class="view">
          Маршруты Альтернативного Путеводителя
        </h1>
      </div>
    </div>
    <Breadcrumbs :crumbs="[{name: 'Маршруты', url: '' }]" />
    <Gallery :objects="routes" type="route" :loading="loading" />
    <div class="row">
      <div class="col-12">
        <b-pagination
          v-if="pages > 1"
          v-model="page"
          :total-rows="pages"
          :per-page="perPage"
          aria-controls="my-table"
        />
      </div>
    </div>
  </div>
</template>
<script>
import axios from 'axios'

export default {
  data () {
    return {
      routes: [],
      loading: true,
      page: 1,
      pages: null,
      perPage: 12,
    }
  },
  async fetch () {
    await this.fetchRoutes()
  },
  head: {
    title: 'Карта маршрутов для самостоятельных путешественников',
    meta: [
      {
        name: 'description',
        content: 'Каталог маршрутов на карте. Для самостоятельной организации путешествия!'
      }
    ]
  },
  watch: {
    page: {
      handler (val) {
        this.fetchRoutes()
      }
    }
  },
  mounted () {
    this.fetchRoutes()
  },
  methods: {
    async fetchRoutes () {
      this.loading = true
      const { data } = await axios.get('/routes', { params: { page: this.page, perPage: this.perPage } })
      this.routes = data.data
      this.pages = data.meta.total
      this.loading = false
    }
  }
}
</script>

<style>

</style>
