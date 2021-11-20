<template>
  <div class="container page">
    <div class="row">
      <div class="col-sm-12">
        <h1 class="view">
          <b-row>
            <b-col cols="8">
              <b-form-input v-if="edit" v-model="poi.name" />
              <span v-else>
                {{ poi.name }}
              </span>
              <b-skeleton v-if="loading && !edit" width="90%" />
            </b-col>
            <b-col cols="4">
              <b-button-group v-if="user && poi.author === user.login" style="float:right">
                <b-button v-if="!edit" @click="edit = true">
                  Редактировать
                </b-button>
                <b-button v-if="edit" variant="success" @click="save">
                  <b-spinner v-if="loading" style="width: 20px; height: 20px;" />
                  Сохранить
                </b-button>
                <b-button v-if="edit" @click="cancel">
                  Отмена
                </b-button>
              </b-button-group>
            </b-col>
          </b-row>
        </h1>
      </div>
    </div>
    <Breadcrumbs :list="this.poi.locations" />
    <div class="row inner">
      <div class="col-sm-8 object-full">
        <div class="near">
          <b-tabs>
            <b-tab title="Фото">
              <div class="view_image">
                <div id="bigimage">
                  <img :src="'https://altertravel.ru/images/' + $route.params.id + '/1.jpg'" :title="poi.name" class="img-fluid">
                </div>
                <div id="thumbs">
                  <div v-for="i in poi.images" :key="`img_${i.id}`" class="gal-img">
                    <img :src="'https://altertravel.ru/images/' + i.path" :alt="poi.name" class="img-fluid">
                  </div>
                </div>
                <b-form-input v-if="edit" v-model="poi.copyright" placeholder="Автор фотографий" />
                <p v-else>Автор фотографий {{ poi.copyright ? poi.copyright : poi.author }}  <b-skeleton v-if="loading" width="5%" /> ©</p>
              </div>
            </b-tab>
            <b-tab title="Где находится?">
              <span v-if="edit">Вы можете двигать точку на карте</span>
              <client-only>
                <GmapMap
                  v-if="center"
                  :center="center"
                  :zoom="7"
                  map-type-id="terrain"
                >
                  <GmapMarker
                    :position="center"
                    :clickable="true"
                    :draggable="edit"
                    @drag="dragend"
                  />
                </GmapMap>
              </client-only>
              <h2 id="coord">
                Кординаты: <span>{{ poi.lat }},&nbsp;{{ poi.lng }}</span>
              </h2>
            </b-tab>
            <b-tab v-if="poi.ytb" title="Видео">
              <iframe width="700" height="400" :src="'https://www.youtube.com/embed/' + poi.ytb" frameborder="0" allowfullscreen="" />
            </b-tab>
          </b-tabs>
        </div>
      </div>
      <div class="col-sm-4">
        <h2 id="interesting">
          Описание
        </h2>
        <p>
          <Skeleton v-if="loading && !edit" />
          <b-form-textarea v-if="edit" v-model="poi.description" min-rows="3" max-rows="20" no-auto-shrink />
          <span v-else>
            {{ poi.description }}
          </span>
        </p>
      </div>
    </div>
    <div v-if="!edit" class="row">
      <div class="col-sm-12">
        <h2 id="near">
          Что еще есть рядом с эти местом
        </h2>
        <div class="near">
          <b-tabs content-class="mt-3">
            <b-tab title="Ближе всего" active>
              <b-row>
                <b-col v-for="poi in poi.nearest" :key="poi.id" cols="3">
                  <PoiCard :poi="poi" />
                </b-col>
              </b-row>
            </b-tab>
            <b-tab
              v-for="tag in tags"
              :key="tag.value"
              :title="tag.text"
            >
              <b-row>
                <b-col v-for="poi in poi.nearesttags[tag.text]" :key="poi.id" cols="3">
                  <PoiCard :poi="poi" />
                </b-col>
              </b-row>
            </b-tab>
            <b-tab :title="poi.type ? `«${poi.type}»` : ``">
              <p>Контент</p>
            </b-tab>
            <b-tab title="Ночлег">
              <p>Контент</p>
            </b-tab>
          </b-tabs>
        </div>
      </div>
    </div>
    <div v-else class="row">
      <div class="col-10">
        <h2>Метки</h2>
        <Skeleton v-if="loadingTags && !edit" />
        <b-form-checkbox-group
        v-if="!loadingTags"
          v-model="poi.tags"
          :options="alltags"
        />
      </div>
      <div class="col-2">
        <h2>Категория</h2>
        <b-form-select v-model="poi.type">
          <b-form-select-option value="Техноген">Техноген</b-form-select-option>
          <b-form-select-option value="Природа">Природа</b-form-select-option>
          <b-form-select-option value="История-Культура">История-Культура</b-form-select-option>
          <b-form-select-option value="Памятник">Памятник</b-form-select-option>
          <b-form-select-option value="Музей">Музей</b-form-select-option>
          <b-form-select-option value="Архитектура">Архитектура</b-form-select-option>
          <b-form-select-option value="Природа">Природа</b-form-select-option>
        </b-form-select>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <h2 id="route">
          Как добраться на машине
        </h2>
        <Skeleton v-if="loading  && !edit " />
        <b-form-textarea v-if="edit" v-model="poi.route" min-rows="3" max-rows="20" no-auto-shrink />
        <span v-else>
          {{ poi.route }}
        </span>
      </div>
      <div class="col-sm-6">
        <h2 id="route_o">
          Как добраться на общественном транспорте
        </h2>
        <Skeleton v-if="loading && !edit" />
        <b-form-textarea v-if="edit" v-model="poi.route_o" min-rows="3" max-rows="20" no-auto-shrink />
        <span v-else>
          {{ poi.route_o }}
        </span>
      </div>
    </div>
    <b-row>
      <b-col cols="12">
        <h2 id="addon">
          Примечание
        </h2>
        <Skeleton v-if="loading && !edit" />
        <b-form-textarea v-if="edit" v-model="poi.addon" min-rows="3" max-rows="20" no-auto-shrink />
        <span v-else>
          {{ poi.addon }}
        </span>
      </b-col>
    </b-row>
    <b-row>
      <b-col cols="12">
        <b-button-group v-if="user && poi.author === user.login" style="float:right">
          <b-button v-if="!edit" @click="edit = true">
            Редактировать
          </b-button>
          <b-button v-if="edit" variant="success" @click="save">
            <b-spinner v-if="loading" style="width: 20px; height: 20px;" />
            Сохранить
          </b-button>
          <b-button v-if="edit" @click="cancel">
            Отмена
          </b-button>
        </b-button-group>
      </b-col>
    </b-row>
    <Comments :id="$route.params.id" type="poi" />
  </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'

export default {
  data () {
    return {
      poi: {
        tags: [],
      },
      loading: true,
      edit: false,
      alltags: [],
      loadingTags: false,
    }
  },
  head () {
    return {
      title: this.poi.name,
      meta: [
        {
          name: 'description',
          content: 'todo'
        }
      ]
    }
  },
  computed: {
      ...mapGetters({
        authenticated: 'auth/check',
        user: 'auth/user'
      }),
      tags: {
        get: function() {
          return this.alltags.filter((item) => this.poi.tags.includes(item.value))
        }
      },
      center: {
        get: function() {
          if (this.poi.lat) {
            return { lat: this.poi.lat, lng: this.poi.lng }
          } else {
            return { lat: 0, lng: 0 }
          }
        }
      }
  },
  created () {
    this.fetchAllTags();
  },
  async fetch () {
    this.loading = true;
    const { data } = await axios.get(
      '/pois/' + this.$route.params.id
    )
    this.poi = data.data
    this.loading = false;
  },
  methods: {
    async fetch () {
      this.loading = true;
      const { data } = await axios.get(
        '/pois/' + this.$route.params.id
      )
      this.poi = data.data
      this.loading = false;
    },
    async fetchAllTags() {
      this.loadingTags = true;
      const { data } = await axios.get('/tags', { params: { all: 1 } })
      this.alltags = data
      this.loadingTags = false;
    },
    async save() {
      this.loading = true;
      const { data } = await axios.patch('/pois/' + this.poi.id, { ...this.poi })
      this.poi = data.data
      this.edit = false;
      this.loading = false;
    },
    cancel() {
      this.fetch();
      this.edit = false;
    },
    dragend(location) {
      this.poi.lat = location.latLng.lat();
      this.poi.lng = location.latLng.lng();
    }
  }
}
</script>

<style scoped>
  .near, .comments_tabz {
    background: #7495AA;
    padding: 5px;
    border-radius: 3px;
    margin-bottom: 5px;
    overflow: hidden;
  }
  ul.tabs li.taba.active a, ul.tabs li.taba1.active a, ul.tabs li.taba.active, ul.tabs li.taba1.active, ul.tabs li.taba_c.active a, ul.tabs li.taba_c.active {
    color: #7495AA;
  }
  ul.tabs li.active {
    background: #eee;
    border-top-right-radius: 3px;
    border-top-left-radius: 3px;
  }
  ul.tabs li.taba a, ul.tabs li.taba1 a, ul.tabs li.taba, ul.tabs li.taba1, ul.tabs li.taba_c a, ul.tabs li.taba_c {
    color: #FFF;
    line-height: 35px;
    height: 42px;
    display: inline-block;
  }
  ul.tabs img {
    display: inline-block;
    vertical-align: middle;
    border-radius: 4px;
  }
  ul.pages {
    background: #FFF;
  }
  li.vkladka, li.vkladka_c, li.vkladka1 {
    display: none;
  }
  li.vkladka.active, li.vkladka1.active, li.vkladka_c.active {
    display: block;
  }
  ul.pages > li {
    background: #eee;
    box-shadow: 2px 2px 5px 0px rgb(50 50 50 / 50%);
  }
  .gal-img {
    position: relative;
    margin-bottom: 12px;
    text-align: center;
    max-width: 150px;
    display: inline-block;
    margin-right: 10px;
    margin-bottom: 10px;
  }
</style>
