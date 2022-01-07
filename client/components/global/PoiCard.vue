<template>
  <div class="poi__card">
    <div :class="loading ? 'poi_card loading' : 'poi__content'">
      <div v-if="loading" class="spinner">
        <b-spinner />
      </div>
        <nuxt-link :to="'/poi/' + poi.id">
          <b-img-lazy
            :src="'https://altertravel.ru/thumb.php?f=/images/' + poi.main_image"
            :alt="poi.name"
            :blank-color="poi.dominatecolor"
            fluid
          />
        </nuxt-link>
      <div v-if="user && poi.author === user.login" class="your">
        опубликовали вы
      </div>
      <div class="poi__card__text">
        <nuxt-link :to="'/poi/' + poi.id" class="namelink">
          <client-only>
            <b-icon 
              :icon="getIcon(poi.id)" 
              aria-hidden="true" 
              variant="warning" 
              @click.prevent="toggleChosen(poi.id)"
              class="chosen"
            />
          </client-only>
          <span v-html="poi.name" />
        </nuxt-link>
        <div v-if="poi.dist" class="above_img">
          {{ poi.dist > 10 ? Math.round(poi.dist) : poi.dist }} км
        </div>
        <b-icon-eye /> {{ views }} / <b-icon-chat v-if="poi.comments" /> {{ poi.comments ? poi.comments + ' /' : ''  }} &copy;&nbsp;{{ poi.author }}
      </div>
    </div>
  </div>
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
    },
  },
  data() {
    return {
      chosen: [],
    }
  },
  computed: {
    ...mapGetters({ user: 'auth/user'}),
    views: {
      get: function () {
        if (this.poi.views > 1000) {
          return Math.round(this.poi.views / 1000) + 'k';
        }
        return this.poi.views;
      },
    },
  },
  mounted() {
  },
  methods: {
    toggleChosen(id) {
      if (this.localStorage.chosen.includes(id)) {
        const index = this.localStorage.chosen.indexOf(id);
        if (index > -1) {
          this.localStorage.chosen.splice(index, 1);
        }
      } else {
        this.localStorage.chosen.push(id);
      }
    },
    getIcon(id) {
      if(process.client && localStorage.chosen) {
        return localStorage.chosen.indexOf(id) > -1 ? 'star-fill' : 'star';
      } else {
        return 'star';
      }
    }
  }
}
</script>

<style>
  .loading {
    opacity: 0.4;
  }
  .spinner {
    width:90%;
    height: 90%;
    display: flex;
    position: absolute;
    align-items: center;
    justify-content: center;
    background-color: rgba(255,255,255, 0.5);
  }
  .poi__card {
    box-shadow: 4px 4px 8px 0px rgba(34, 60, 80, 0.2);
  }
  .namelink {
    text-decoration: none;
    height: 44px;
    font-size: 18px;
    overflow:hidden;
    display:block;
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
    height: 90px;
  }
  .your {
    background-color: #fff;
    display: block;
    color: green;
    position:absolute;
    right:0;
    top:0;
    opacity: 0.8;
    padding: 3px;
    font-size:14px;
  }
</style>


<style scoped>
  .chosen {
    cursor: pointer;
  }
  .above_img {
    position: absolute;
    z-index: 1;
    top: 0;
    width: 100%;
    margin-top: 50%;
    margin-left: -15px;
    text-align: center;
    font-size: 2em;
    color: rgba(255,255,255,0.8);
    text-shadow: #000 2px 3px 5px;
}
</style>
