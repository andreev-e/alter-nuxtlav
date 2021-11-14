<template>
  <nuxt-link
    :to="'/routes/' + poi.id"
    class="poi__card"
  >
    <div :class="loading ? 'poi_card loading' : 'poi__content'">
      <div v-if="loading" class="spinner">
        <b-spinner />
      </div>
      <img :src="`https://altertravel.ru/routes/${poi.id}/1.jpg`" :title="poi.name" class="img-fluid">
      <div class="img-title">
        {{ poi.name }}
        <div class="icons">
          <span v-if="poi.days"><b-icon-clock />{{ poi.days }}</span>
          <span v-if="poi.views"><b-icon-bar-chart />{{ poi.views }}</span>
        </div>
      </div>
      
    </div>
  </nuxt-link>
</template>

<script>
export default {
  name: 'RouteCard',
  props: {
    poi: {
      type: Object,
      required: true
    },
    loading: {
      type: Boolean,
      default: false
    }
  }
}
</script>

<style scoped>
  .loading {
    opacity: 0.4;
  }
  .spinner {
    width:100%;
    height: 100%;
    display: flex;
    position: absolute;
    align-items: center;
    justify-content: center;
    background-color: rgba(255,255,255, 0.5);
  }
  .poi__card {
    display: block;
    padding: 15px 0;
  }
  .poi__card a {
    text-decoration: none;
  }
  .poi__content {
    position: relative;
  }
  .img-title {
    opacity: 0.9;
    background: #7495AA;
    color: #FFF;
    font-size: 18px;
    text-align: center;
    z-index: 1;
    padding: 0;
    box-shadow: 0 4px 4px -4px rgb(0 0 0);
    overflow: hidden;
    line-height: 22px;
  }
  .icons {
    text-align:right;
    padding: 5px 10px;
  }
  .icons svg {
    margin-right: 3px;
  }
</style>
