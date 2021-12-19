import Vue from 'vue'
import reactiveStorage from 'vue-reactive-storage';
// Set initial values
if (process.client) 
{
    Vue.use(reactiveStorage, {
        'chosen': [],
        'startLocation' : { lat: 55.7522, lng: 37.6156 },
        'finishLocation' : { lat: 55.7622, lng: 37.6356 }
    });
}