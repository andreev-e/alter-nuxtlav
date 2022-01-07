<template>
  <div class="container page">
    <Header />
    <Breadcrumbs 
      :crumbs="[{name: 'Каталог', url: '/catalog' }, {name: tag.name, url: '' }]"
      :loading="loadingTag" 
    />
    <div class="row">
      <div class="col-sm-12">
        <h1>
          {{ tag.NAME_ROD ? tag.NAME_ROD + '. Достопримечательности на карте' : tag.name }}
          <b-skeleton v-if="!tag.name" width="75%" />
        </h1>
      </div>
    </div>
    <Map :center="center" :tag="$route.params.id" />
    <Gallery :objects="pois" :loading="loadingPois" />
    <div class="row">
      <div class="col-12">
        <b-pagination
          v-if="pages > 0"
          v-model="page"
          :total-rows="pages"
          :per-page="perPage"
          aria-controls="my-table"
        />
      </div>
    </div>
    <Footer />
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data () {
    return {
      loadingTag: false,
      pois: [],
      loadingPois: true,
      center: { lat: null, lng: null },
      page: 1,
      pages: null,
      perPage: 12,
      tag: {
        name: null,
        NAME_ROD: null,
        count: null,
      },
    }
  },
  async fetch () {
    this.id = this.$route.params.id
    await this.fetchPois()
    await this.fetchTag()
  },
  head() {
    return {
      title: 'Все ' + this.tag.NAME_ROD + ' на карте. Топ ' + this.tag.count + ' достопримечательность',
      meta: [
        {
          hid: 'description',
          name: 'description',
          content: 'Все ' + this.tag.NAME_ROD + ' в путеводителе с фото, описаниями, отзывами, картами проезда. Достопримечательности.'
        }
      ]
    }
  },
  watch: {
    page: {
      handler (val) {
        this.fetchPois()
      }
    }
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
    this.fetchTag()
  },
  methods: {
    async fetchTag () {
      console.log('fetchTag')
      const { data } = await axios.get('/tags/' + this.id)
      this.tag = data.tag
      if (process.client) {
        this.$refs.map.fetchPoisToMap();
      }
      this.loadingTag = false
    },
    async fetchPois () {
      this.loadingPois = true
      const { data } = await axios.get(
        '/pois',
        { params: { tag: this.id, page: this.page, limit: this.perPage } }
      )
      this.pois = data.data
      this.pages = data.meta.total
      this.loadingPois = false
    }
  }
}
</script>
