<template>
  <div class="row nopadding">
    <div class="legend-title col-sm-12">
      <b>Легенда карты и фильтры</b>
      <div style="display:flex; align-items:center; justify-content: space-around">
        <b-form-checkbox-group
          v-model="selectedTypes"
          :options="typesSelect"
          @change="filterTypes"
        />
      </div>
      <hr v-if="mode === 'chosen'">
      <b-row v-if="mode === 'chosen'">
        <b-col sm="6">
          <label for="otdalenie">Отклонение от маршрута, {{ otdalenie }} км:</label>
        </b-col>
        <b-col sm="5">
          <b-input 
            id="otdalenie" 
            v-model="otdalenie" 
            min="5" 
            max="50" 
            type="range"
            @change="recountAdditional"
          />
        </b-col>
      </b-row>
    </div>
    <div class="mapspinner">
      <b-spinner v-if="loading" />
    </div>
    <div class="mapcomment">
      <span v-if="total">Маршрут длиной {{ total }} км</span>
    </div>
    <client-only>
      <GmapMap
        ref="map"
        :center="center"
        :zoom="zoom"
        map-type-id="terrain"
        :options="mapOptions"
        @dragend="mapMoved"
        @zoom_changed="mapMoved"
        @idle="mapIdle"
      >
        <GmapMarker
          v-if="mylocation"
          :position="mylocation"
          :clickable="true"
          :icon="mylocationMarker"
        />
        <GmapMarker
          v-if="chosen.length"
          :position="localStorage.startLocation"
          :icon="startMarker"
          :draggable="true"
          @dragend="dragStart"
        />
        <GmapMarker
          v-if="chosen.length"
          :position="localStorage.finishLocation"
          :icon="finishMarker"
          :draggable="true"
          @dragend="dragFinish"
        />
        <GmapMarker
          v-for="poi in chosen"
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
              pixelOffset: { width: 0, height: -1 },
            }"
          >
            <span v-html="poi.name" />
          </gmap-info-window>
        </GmapMarker>
        <GmapCluster :gridSize="70" :zoomOnClick="true">
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
                pixelOffset: { width: 0, height: -1 },
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
import GmapCluster from "vue2-google-maps/dist/components/cluster";
import * as VueGoogleMaps from 'vue2-google-maps'   
import axios from 'axios'
import { faIndustry, faMonument, faPlaceOfWorship, faLandmark, faTree, faMapMarker, faIgloo } from "@fortawesome/free-solid-svg-icons";

let directions = null;
let directionsDisplay = null;

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
    chosen: {
      type: Array,
      default: () => []
    },
    mode: {
      type: String,
      default: null
    }
  },
  data () {
    return {
      initialLoadDone: false,
      mylocation: false,
      otdalenie: 5,
      total: 0,
      mylocationMarker: {
        url: 'https://maps.google.com/mapfiles/kml/shapes/man.png',
        size: { width: 64, height: 64, f: 'px', b: 'px' },
        scaledSize: { width: 64, height: 64, f: 'px', b: 'px' }
      },
      startMarker: {
        url: 'https://altertravel.ru/i/start.png',
      },
      finishMarker: {
        url: 'https://altertravel.ru/i/end.png',
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
      types: [
        'Архитектура',
        'История/Культура',
        'Природа',
        'Техноген',
        'Памятник',
        'Музей',
        'Ночлег',
        'Еда',
        'Покупки'
      ],
      selectedTypes: [],
      source: null,
      logarifm: 4,
      bounds: null,
      additional: [],
      route: [],
    }
  },
  computed: {
    google: VueGoogleMaps.gmapApi,
    alreadyLoaded: {
      get:function () {
        if (this.mappois.length) {
          return this.mappois.map((val) => val.id).join(',');
        }
        else {
          return null;
        }
      }
    },
    typesSelect: {
      get: function () {
        return this.types.map((item) => ({ text: item, value: item }));
      },
    },
    poisForRoute: {
      get: function () {
        return this.chosen.map((item) => ({ location: { lat: item.lat, lng: item.lng }, stopover: true }));
      },
    },
  },
  watch: {
    chosen: {
      handler () {
        this.calcRoute();
      }
    }
  },
  mounted() {
    this.selectedTypes = this.types;
  },
  methods: {
    async fetchPoisToMap (force = false) {
      console.log('fetchPoisToMap');
      if (!this.loading && this.$refs.map && this.$refs.map.$mapObject && this.center.lat !== 0) {
        const bounds = this.$refs.map.$mapObject.getBounds()
        if (!force && !bounds) {
          console.log('nobounds');
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
                  bounds: this.mode !== 'chosen' ? bounds.toUrlValue() : null, 
                  types: this.selectedTypes.lenth < 9 ? this.selectedTypes.lenth : [] , 
                  alreadyLoaded: this.alreadyLoaded,
                  additional: this.mode === 'chosen' ? this.additional.join('!') : null,
                  otdalenie: this.mode === 'chosen' ? this.otdalenie : null,
                },
                cancelToken: this.source.token
              }
            )
            data.data = data.data.map(item => ({ ...item, showInfoWindow: false }))
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
    },
    async calcRoute() {
      console.log('calcRoute');
      this.loading = true;
      const start = { location: this.localStorage.startLocation };
      const end = { location: this.localStorage.finishLocation };
      var request = {
          origin: start,
          destination: end,
          waypoints: this.poisForRoute, 
          optimizeWaypoints: true,
          travelMode: google.maps.DirectionsTravelMode.DRIVING
      };
      console.log(request);

      if (directions === null) {
        directions = new google.maps.DirectionsService();
        directionsDisplay = new google.maps.DirectionsRenderer();
        directionsDisplay.setMap(this.$refs.map.$mapObject);
        directionsDisplay.setOptions({ suppressMarkers: true });
      }

      await directions.route(request)
        .then((response) => {
          this.additional = [];
          directionsDisplay.setDirections(response);
          this.totalDistance(directionsDisplay.directions);
          this.route = response.routes[0];
          this.recountAdditional();
      });
      this.loading = false;
      this.fetchPoisToMap();
    },
    totalDistance(result) {
      var myroute = result.routes[0].legs;
      this.total = myroute.reduce((acc, item) => acc + item.distance.value, 0);
      console.log(this.total);
      this.total = this.total / 1000;
      this.total = Math.round(this.total * 10) / 10
      if (this.total > 1000) { 
        this.otdalenie = 50; 
      }
    },
    dragStart (location) {
      this.localStorage.startLocation.lat = location.latLng.lat();
      this.localStorage.startLocation.lng = location.latLng.lng();
      this.calcRoute();
    },
    dragFinish (location) {
      this.localStorage.finishLocation.lat = location.latLng.lat();
      this.localStorage.finishLocation.lng = location.latLng.lng();
      this.calcRoute();
    },
    filterTypes () {
      if (this.selectedTypes.length === 0) {
        this.selectedTypes = this.types;
      }
      this.mappois = this.mappois.filter(item => this.selectedTypes.indexOf(item.type)>-1)
    },
    recountAdditional () {
      console.log('recountAdditional');
      let prevpoint = this.startLocation;
      let prevpoint2 = this.startLocation;
      if (this.route) {
        this.route.legs.forEach(leg => {
          leg.steps.forEach(step => {
            step.path.forEach(point => {
              let lat1 = point.lat();
              let lng1 = point.lng();
              if (google.maps.geometry.spherical.computeDistanceBetween(prevpoint2, point) > this.otdalenie * 100) {
                prevpoint2 = point;
              }
              if (google.maps.geometry.spherical.computeDistanceBetween(prevpoint, point) > this.otdalenie * 750) {
                this.additional.push(lat1.toFixed(4)+';'+lng1.toFixed(4));
                prevpoint = point;
              }
            });
          });
        });
      }
      this.mappois = [];
      this.fetchPoisToMap();
    },
    mapMoved () {
      if (this.mode !== 'chosen') { 
        this.fetchPoisToMap();
      }
    },
    mapIdle () {
      if (!this.initialLoadDone) {
        if (this.mode === 'chosen') { 
          this.calcRoute();
        } else {
          this.fetchPoisToMap();
        }
        this.initialLoadDone = true;
      }
    },
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
  width: 50px;
  height: 50px;
  display: flex;
  position: absolute;
  left: 0;
  bottom: 0;
  align-items: center;
  justify-content: center;
  z-index: 2;
}
.mapcomment {
  position: absolute;
  width:100%;
  left: 0;
  bottom: -30px;
  display: flex;
  justify-content: center;
  z-index: 2;
  background-color:#ebf1f5;
}
.legend-title {
  color: #fff;
  background: #7495AA;
  margin: 0 auto;
  text-align: center;
  vertical-align: middle;
  padding: 3px 0;
  position: relative;
  z-index: 0;
  text-decoration: none;
  cursor: pointer;
}
</style>
