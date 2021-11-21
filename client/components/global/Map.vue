<template>
  <div class="row nopadding">
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
        <GmapMarker
          v-if="chosen.length"
          :position="startLocation"
          :icon="startMarker"
          :draggable="true"
          @dragend="dragStart"
        />
        <GmapMarker
          v-if="chosen.length"
          :position="finishLocation"
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
    types: {
      type: Array,
      default: () => []
    },
    chosen: {
      type: Array,
      default: () => []
    }
  },
  data () {
    return {
      mylocation: false,
      otdalenie: 50,
      total: 0,
      startLocation: { lat: 55.7522, lng: 37.6156 },
      finishLocation: { lat: 55.7622, lng: 37.6356 },
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
      source: null,
      logarifm: 4,
      bounds: null,
    }
  },
  watch: {
    chosen: function (val) {
      console.log('chosenChanged', val);
    },
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
    poisForRoute: {
      get: function () {
        return this.chosen.map((item) => ({ location: { lat: item.lat, lng: item.lng }, stopover: true }));
      },
    },
  },
  watch: {
    types: function (val) {
      this.fetchPoisToMap();
    },
    center: function (val) {
      this.fetchPoisToMap();
    },
    chosen: function (val) {
      this.calcRoute();
    },
  },
  methods: {
    async fetchPoisToMap () {
      if (this.chosen.length) {
        return;
      }
      console.log('fetchPoisToMap');
      if (!this.loading && this.$refs.map && this.$refs.map.$mapObject && this.center.lat !== 0) {
        const bounds = this.$refs.map.$mapObject.getBounds()
        if (!bounds) {
          console.log('nobounds');
          return;
        }
        // if (this.bounds === bounds.toUrlValue()) {
        //   console.log('equalbounds');
        //   return;
        // }
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
                  types: this.types.lenth < 9 ? this.types.lenth : [] , 
                  alreadyLoaded: this.alreadyLoaded,
                  only: this.chosen.length ? this.chosen : null,
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
      if (this.chosen.length) {
        this.calcRoute();
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
    calcRoute() {
      this.loading = true;
      console.log('calcRoute');
      const start = { location: this.startLocation };
      const end = { location: this.finishLocation };
      var request = {
          origin: start,
          destination: end,
          waypoints: this.poisForRoute, optimizeWaypoints: true,
          travelMode: google.maps.DirectionsTravelMode.DRIVING
      };

      if (directions === null) {
        directions = new google.maps.DirectionsService();
        directionsDisplay = new google.maps.DirectionsRenderer();
        directionsDisplay.setMap(this.$refs.map.$mapObject);
        directionsDisplay.setOptions({ suppressMarkers: true });
      }

      directions.route(request)
        .then((response) => {
          console.log(response);
          directionsDisplay.setDirections(response);
          this.totalDistance(directionsDisplay.directions);
          // var myRoute = response.routes[0].legs[0];
          // var route = response.routes[0];
          // var points_search = '';
          // var cont_steps = 0;
          // var prevpoint = start;
          // var prevpoint2 = start;
          // const all_points = [];
          // route.legs.forEach(function (leg) {
          //   leg.steps.forEach(function (step) {
          //     step.path.forEach(function(point) {
          //       let latlng = point.toString();
          //       latlng = latlng.replace(')','');
          //       latlng = latlng.replace('(','');
          //       let latlng_arr =  latlng.split(',');
          //       let lat1 = parseFloat(latlng_arr[0]);
          //       let lng1 = parseFloat(latlng_arr[1]);
          //       if (google.maps.geometry.spherical.computeDistanceBetween(prevpoint2, point) > 50 * 100) {
          //         all_points.push(point);
          //         prevpoint2=point;
          //       }
          //       if (google.maps.geometry.spherical.computeDistanceBetween(prevpoint, point) > 50 * 750) {
          //         points_search=points_search+lat1.toFixed(4)+'%'+lng1.toFixed(4)+'!';
          //         cont_steps = cont_steps+1;
          //         prevpoint = point;
          //       }
          //     });
          //   });
          // });
      });
      this.loading = false;
    },
    totalDistance(result) {
      var myroute = result.routes[0].legs;
      this.total = myroute.reduce((acc, item) => acc + item.distance.value, 0);
      console.log(this.total);
      this.total = this.total / 1000;
      this.total = Math.round(this.total * 10) / 10
      // if (this.total > 1000) { 
      //   this.otdalenie = 50; 
      // }
    },
    dragStart (location) {
      this.startLocation.lat = location.latLng.lat();
      this.startLocation.lng = location.latLng.lng();
      this.calcRoute();
    },
    dragFinish (location) {
      this.finishLocation.lat = location.latLng.lat();
      this.finishLocation.lng = location.latLng.lng();
      this.calcRoute();
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
</style>
