<template>
  <div class="container page">
    <div class="row">
      <div class="col-sm-12">
        <h1>
          {{ tag.NAME_DAT_ED ? 'Достопримечательности ' + tag.NAME_DAT_ED + ' на карте' : tag.name }}
          <b-skeleton v-if="!tag.name" width="50%" />
        </h1>
      </div>
    </div>
    <Breadcrumbs :crumbs="breadcrumbs" :loading="loadingRegion" />
    <TwoPanels :left="tag.children" :right="[]" />
    <Map ref="map" :center="center" :zoom="tag.zoom" />
    <div class="row">
      <div class="col-12">
        <h2>Достопримечательности</h2>
      </div>
    </div>
    <Gallery :objects="pois" :loading="loadingPois" />
    <div class="row">
      <div class="col-12">
        <b-pagination
          v-if="pages / perPage > 1"
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
      pois: [],
      tag: {
        name: '',
        lat: 0,
        lng: 0,
        locations: [],
      },
      center: { lat: process.env.defaulLat, lng: process.env.defaultLng },
      page: 1,
      pages: null,
      perPage: 12,
      loadingPois: true,
      loadingRegion: true
    }
  },
  computed: {
    breadcrumbs: {
      get: function() {
        return [{name: 'Каталог', url: '/catalog' }, ...this.tag.locations]
      },
    },
  },
  async fetch () {
    this.id = this.$route.params.id
    await this.fetchTag()
    await this.fetchPois()
  },
  head() {
    return {
      title: 'Топ ' + (this.tag.count < 100 ? this.tag.count : 100) + ' достопримечательностей ' + this.tag.NAME_ROD_ED + ' на карте.',
      meta: [
        {
          name: 'description',
          content: 'Достопримечательности в путеводителе по ' + this.tag.NAME_DAT_ED + '. Фотографии мест, карты, отзывы.'
        }
      ]
    }
  },
  watch: {
    page: {
      handler () {
        this.fetchPois()
      }
    }
  },
  mounted () {
  },
  methods: {
    async fetchTag () {
      const { data } = await axios.get('/tags/' + this.id)
      this.tag = data.tag;
      this.center.lat = this.tag.lat;
      this.center.lng = this.tag.lng;
      if (process.client) {
        this.$refs.map.fetchPoisToMap();
      }
      this.loadingRegion = false
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

<style>

</style>
