<template>
  <div class="container page">
    <div class="row">
      <div class="col-sm-12">
        <h1 class="view">
          Карта Альтернативного Путеводителя
        </h1>
      </div>
    </div>
    <Legend v-model="types" />
    <Map :center="center" :types="types" />
    <Gallery :objects="pois" :loading="loadingPois" />
    <div class="row">
      <div class="col-sm-12">
        <h2>Последние комментарии</h2>
      </div>
    </div>
    <Comments />
  </div>
</template>
<script>
import axios from 'axios'

export default {
  data () {
    return {
      pois: [],
      loadingPois: true,
      center: {},
      types: [],
    }
  },
  async fetch () {
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
    this.fetchPois()
  },
  methods: {
    async fetchPois () {
      this.loadingPois = true
      const { data } = await axios.get('/pois')
      this.pois = data.data
      this.loadingPois = false
    }
  }
}
</script>

<style>

  h1.view {
      background: url(/i/star_grey.png) no-repeat left top;
      padding-left: 35px;
      cursor: pointer;
  }
</style>
