<template>
  <div class="container page">
    <div class="row">
      <div class="col-sm-12">
        <h1 class="view">
          {{ tag.name }}
          <b-skeleton v-if="!tag.name" width="50%" />
        </h1>
      </div>
    </div>
    <Breadcrumbs :list="tag.locations" />
    <TwoPanels :left="tag.children" :right="[]" />
    <Map :center="{ lat: tag.lat, lng: tag.lng }" :zoom="tag.zoom" />
    <div class="row">
      <div class="col-12">
        <h2>Достопримечательности</h2>
      </div>
    </div>
    <Gallery :objects="pois" :loading="loadingPois" />
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
      pois: [],
      tag: {
        name: '',
        lat: 0,
        lng: 0,
      },
      page: 1,
      pages: null,
      perPage: 12,
      loadingPois: true,
      loadingRegion: true
    }
  },
  async fetch () {
    this.id = this.$route.params.id
    await this.fetchTagBackend()
    await this.fetchPois()
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
  watch: {
    page: {
      handler (val) {
        this.fetchPois()
      }
    }
  },
  mounted () {
  },
  methods: {
    async fetchTagBackend () {
      const { data } = await axios.get('/tags/' + this.id)
      this.tag = data.tag
      this.loadingRegion = false
    },
    async fetchTag () {
      const { data } = await axios.get('/tags/' + this.id)
      this.tag = data.tag
    },
    async fetchPois () {
      this.loadingPois = true
      const { data } = await axios.get(
        '/pois',
        { params: { tag: this.id, page: this.page, perPage: this.perPage } }
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
