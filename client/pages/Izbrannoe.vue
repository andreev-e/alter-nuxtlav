<template>
  <div class="container page">
    <div class="row">
      <div class="col-sm-12">
        <h1>
          Маршут с избранными точками
        </h1>
      </div>
    </div>
    <Breadcrumbs :crumbs="[{name: 'Избранное', url: '' }]" />
    <Map :chosen="pois" mode="chosen" :center="center" />
    <div class="row">
      <div class="col-12 text-center m-3">
        <b-spinner v-if="loadingPois && pois.length === 0" />
        <span v-if="!loadingPois && pois.length === 0">Пока ничего нет в избранном. Вы можете добавить объекты в каталоге, нажав на <b-icon-star /> около названия.<br> После этого здесь вы сможете построить маршрут с выбранными точками</span>
      </div>
      <div v-for="poi in pois" :key="poi.id" class="col-sm-3">
        <PoiCard :poi="poi" :loading="loadingPois" />
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      loadingPois: true,
      pois: [],
      center: { lat: null, lng: null },
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
    this.fetchPois()
  },
  watch: {
    localStorage: {
      handler () {
        this.fetchPois();
      },
      deep: true,
    },
  },
  methods: {
    async fetchPois () {
      this.loadingPois = true;
      if (process.client && this.localStorage.chosen.length) {
        const { data } = await axios.get(
          '/pois',
          { 
            params: { 
              only: this.localStorage.chosen.join(),
            },
          }
          )
        this.pois = data.data;
      }
      this.loadingPois = false;
    }
  }
}
</script>

<style>

</style>