<template>
  <div class="row nopadding">
    <div class="mapspinner">
      <b-spinner v-if="loading" />
      <span v-if="!loading">{{ mappois.length }}</span>
    </div>
    <client-only>
      <GmapMap
        ref="map"
        :center="center"
        :zoom="zoom"
        map-type-id="terrain"
        :options="mapOptions"
        @dragend="fetchPoisToMap"
        @zoom_changed="fetchPoisToMap"
        @idle="fetchPoisToMap"
      >
        <GmapMarker
          v-if="mylocation"
          :position="mylocation"
          :clickable="true"
          :icon="mylocationMarker"
        />
        <GmapCluster :gridSize="30" :zoomOnClick="true">
          <GmapMarker
            v-for="poi in mappois"
            :key="poi.id + 'marker'"
            :position="{ lat: poi.lat, lng: poi.lng }"
            :clickable="true"
            @click="open('/poi/' + poi.id)"
            :icon="getIcon(poi)"
            @mouseover="poi.showInfoWindow = true"
            @mouseout="poi.showInfoWindow = false"
          >
            <gmap-info-window
              v-if="poi.showInfoWindow"
              :opened="true"
              :options="{
                maxWidth: 200,
                pixelOffset: { width: 0, height: -1 }
              }"
            >
              <span v-html="poi.name" />
            </gmap-info-window>
          </GmapMarker>
        </GmapCluster>  
      </GmapMap>
    </client-only>
  </div>
</template>

<script>
import { gmapApi } from 'vue2-google-maps/src/main'
import GmapCluster from "vue2-google-maps/dist/components/cluster";
import axios from 'axios'
import { faIndustry, faMonument, faPlaceOfWorship, faLandmark, faTree, faMapMarker, faIgloo } from "@fortawesome/free-solid-svg-icons";

export default {
  name: 'Map',
  components: { GmapCluster },
  prop: {
    value: {
      type: Object
    }
  },
  props: {
    center: {
      type: Object,
      default: () => { return { lat: 55, lng: 45 } }
    },
    tag: {
      type: String,
      default: null
    },
    zoom: {
      type: Number,
      default: 7
    },
    my: {
      type: Number,
      default: null
    },
    types: {
      type: Array,
      default: () => []
    },
    chosen: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      mylocation: false,
      mylocationMarker: {
        url: 'https://maps.google.com/mapfiles/kml/shapes/man.png',
        size: { width: 64, height: 64, f: 'px', b: 'px' },
        scaledSize: { width: 64, height: 64, f: 'px', b: 'px' }
      },
      showByIndex: null,
      mappois: [],
      loading: false,
      icons: {
        Техноген: [faIndustry, '#A5371E'],
        Архитектура: [faPlaceOfWorship, '#BD832B'],
        'История-Культура': [faIgloo, '#0684BF'],
        Природа: [faTree, '#30B44A'],
        Музей: [faLandmark, '#8E51A0'],
        Памятник: [faMonument, '#DF0386'],
      },
      mapOptions: {
        gestureHandling: 'greedy', 
        treetViewControl: false,
        styles: [
          {
            featureType: "poi",
            elementType: "labels",
            stylers: [{ visibility: "off" }]
          }
        ]
      },
      source: null,
      logarifm: 4,
      bounds: null,
    }
  },
  computed: {
    google: gmapApi,
    alreadyLoaded: {
      get:function () {
        return this.mappois.map((val) => val.id).join(',');
      }
    }
  },
  mounted () {
    if (this.chosen) {
      this.fetchPoisToMap();
    }
  },
  watch: {
    types: function (val) {
      this.fetchPoisToMap();
    },
    center: function (val) {
      this.fetchPoisToMap();
    },
  },
  methods: {
    async fetchPoisToMap () {
      if (!this.loading && this.$refs.map && this.$refs.map.$mapObject && this.center.lat !== 0) {
        const bounds = this.$refs.map.$mapObject.getBounds()
        if (!bounds) {
          return;
        }
        if (this.bounds === bounds.toUrlValue()) {
          return;
        }
        this.bounds = bounds.toUrlValue();
        if (this.source) {
          this.source.cancel();
        }
        const CancelToken = axios.CancelToken;
        this.source = CancelToken.source();
        if (bounds) {
          try {
            this.loading = true;
            const { data } = await axios.get(
              '/pois',
              { 
                params: { 
                  tag: this.tag, 
                  my: this.my, 
                  bounds: bounds.toUrlValue(), 
                  types: this.types, 
                  alreadyLoaded: this.alreadyLoaded,
                  only: this.chosen ? this.localStorage.chosen.join() : null,
                },
                cancelToken: this.source.token
              }
            )
            data.data = data.data.map(item => ({ ...item, showInfoWindow: false }))
            //showInfoWindow: false
            this.mappois = [...this.mappois, ...data.data] ;
            this.mappois = this.mappois.filter((mappois, index, self) =>
              index === self.findIndex((t) => (
                t.id === mappois.id
              ))
            )
            this.loading = false
          }
          catch(e) {
            console.log(e);
          }
          finally {
          }
        }
      }
    },
    getIcon(poi) {
      if (this.icons[poi.type]) {
        return {
          path: this.icons[poi.type][0].icon[4],
          fillColor: this.icons[poi.type][1],
          fillOpacity: 1,
          anchor: new google.maps.Point(
            this.icons[poi.type][0].icon[0] / 2, // width
            this.icons[poi.type][0].icon[1] // height
          ),
          strokeWeight: 1,
          strokeColor: "#444",
          scale: 0.01 * Math.log(poi.views + this.logarifm) / Math.log(this.logarifm),
        }
      } else {
        console.log(poi.type);
        return {
          path: faMapMarker.icon[4],
          fillColor: "#777",
          fillOpacity: 1,
          anchor: new google.maps.Point(
            faMapMarker.icon[0] / 2, // width
            faMapMarker.icon[1] // height
          ),
          strokeWeight: 1,
          strokeColor: "#444",
          scale: 0.01 * Math.log(poi.views + this.logarifm) / Math.log(this.logarifm),
        }
      }
    },
    open(url) {
      window.open(url);
    }
  }
}
</script>

<style scoped>
  .vue-map-container {
    height: 450px;
    width: 100%;
  }
  .row.nopadding {
    position: relative;
  }
  .mapspinner {
    width:50px;
    height:50px;
    display: flex;
    position: absolute;
    left: 0;
    bottom: 0;
    align-items: center;
    justify-content: center;
    z-index: 2;
  }
</style>
