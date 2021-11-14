<template>
  <nuxt-link
    :to="'/poi/' + poi.id"
    class="poi__card"
  >
    <div :class="loading ? 'poi_card loading' : 'poi__content'">
      <div v-if="loading" class="spinner">
        <b-spinner />
      </div>
      <img :src="'https://altertravel.ru/thumb.php?f=/images/' + poi.id + '.jpg'" :title="poi.name" class="img-fluid">
      <div v-if="user && poi.author === user.login" class="canedit">
        <b-button><b-icon-pencil /></b-button>
      </div>
      <div class="poi__card__text">
        {{ poi.name }}
        <span class="copyright">&copy;{{ poi.author }}</span>
      </div>
    </div>
  </nuxt-link>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'PoiCard',
  props: {
    poi: {
      type: Object,
      required: true
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  computed: mapGetters({
    authenticated: 'auth/check',
    user: 'auth/user'
  }),
}
</script>

<style>
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
    box-shadow: 0px 0px 4px -4px rgba(0, 0, 0, 0.2);
  }
  .poi__card a {
    text-decoration: none;
  }
  .poi__content {
    position: relative;
  }
  .poi__card__text {
    padding: 10px 15px;
    opacity: 0.9;
    background: #fff;
    color: #444;
    font-size: 16px;
    text-align: left;
    z-index: 1;
    padding: 0;
    overflow: hidden;
    line-height: 22px;
    padding: 15px 5px;
  }
  .copyright {
    float:right;
  }
  .canedit {
    position:absolute;
    right:0;
    top:0;
    opacity: 0.8;
  }
  .canedit:hover {
    opacity: 1;
  }
</style>
