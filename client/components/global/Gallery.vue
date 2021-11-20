<template>
  <div class="row gallery">
    <div class="col-12 text-center m-3"><b-spinner v-if="loading && objects.length === 0" /></div>
    <div v-for="poi in objects" :key="poi.id" class="col-sm-3">
      <PoiCard v-if="type === 'poi'" :poi="poi" :loading="loading" />
      <RouteCard v-if="type === 'route'" :poi="poi" :loading="loading" />
    </div>
  </div>
</template>

<script>

export default {
  name: 'Gallery',
  props: {
    objects: {
      type: Array,
      default: []
    },
    loading: {
      type: Boolean,
      default: false
    },
    type: {
      type: String,
      default: 'poi'
    }
  }
}
</script>

<style>
  .gallery > div {
    margin-bottom:30px;
  }
</style>
