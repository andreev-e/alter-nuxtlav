<template>
  <div class="poi__card">
    <div :class="loading ? 'poi_card loading' : 'poi__content'">
      <div v-if="loading" class="spinner">
        <b-spinner />
      </div>
        <nuxt-link :to="'/poi/' + poi.id">
          <img :src="'https://altertravel.ru/thumb.php?f=/images/' + poi.id + '.jpg'" :title="poi.name" class="img-fluid">
        </nuxt-link>
      <div v-if="user && poi.author === user.login" class="canedit">
        <b-button><b-icon-pencil /></b-button>
      </div>
      <div class="poi__card__text">
        <b-icon 
          :icon="localStorage.chosen.indexOf(poi.id) > -1 ? 'star-fill' : 'star'" 
          aria-hidden="true" 
          variant="warning" 
          @click="toggleChosen(poi.id)"
          class="chosen"
        />
        <nuxt-link :to="'/poi/' + poi.id">{{ poi.name }}</nuxt-link>
        <span class="copyright">&copy;{{ poi.author }}</span>
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
    }
  },
  data() {
    return {
      chosen: [],
    }
  },
  computed: {
    ...mapGetters({ user: 'auth/user'}),
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
    }
  }
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


<style scoped>
  .chosen {
    cursor: pointer;
  }
</style>
