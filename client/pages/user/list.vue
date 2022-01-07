<template>
  <div class="container page">
    <Header />
    <Breadcrumbs 
      :crumbs="[{name: 'Пользователи', url: '' }]"
    />
    <div class="row">
      <div class="col-sm-12">
        <h1>
          Авторы
        </h1>
        <b-row>
          <b-col cols="3">
            <p>Альтернативный путеводитель — проект авторский.
              Все достопримечательности добавляются только членами нашего творческого коллектива.
              Как правило, этому предшествует исследовательская работа и всегда — личное посещение места.</p>
              <p>Нас объединяет любовь к путешествиям, необычным и красивым местам. Особое внимание уделяется качеству фотографий и текстового описания. Для одних из нас путешествия - работа, для других хобби. Главное, что вся представленная информация проверена на личном опыте.</p>
          </b-col>
          <b-col cols="9">
            <div class="col-12 text-center m-3">
              <b-spinner v-if="loadingList" />
            </div>
            <b-row v-for="user in list" :key="user.id">
              <b-col cols="2">
                <b-img-lazy 
                  :src="`https://altertravel.ru/authors/${user.username}.jpg`"
                  rounded
                  fluid-grow 
                />
              </b-col>
              <b-col cols="10">
                <div class="info_title">
                  <router-link :to="`/user/${user.username}`">
                    {{ user.name }} (опубликовано {{ user.publications }} точек)
                  </router-link>
                </div>
                <div class="description">
                  <p>{{ user.about }}</p>
                </div>
                <p>
                  Сайт
                  <a :href="`//${user.homepage}`" target="_blank" rel="nofollow">
                    {{ user.homepage }}
                  </a>
                </p>
                <hr>
              </b-col>
            </b-row>
            <div class="row">
              <div class="col-12">
                <b-pagination
                  v-if="pages > 0"
                  v-model="page"
                  :total-rows="pages"
                  :per-page="perPage"
                  aria-controls="my-table"
                />
              </div>
            </div>
          </b-col>
        </b-row>

      </div>
    </div>

    <Footer />
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data () {
    return {
      list: [],
      page: 1,
      pages: 0,
      perPage: 12,
      loadingList: false,
    }
  },
  watch: {
    page: {
      handler () {
        this.fetchList()
      }
    }
  },
  async fetch () {
    await this.fetchList()
  },
  methods: {
    async fetchList () {
      this.loadingList = true
      const { data } = await axios.get('/users', { params: { page: this.page, limit: this.perPage } })
      this.list = data.data
      this.pages = data.meta.total
      this.loadingList = false
    },
  },
}
</script>

<style>

</style>
