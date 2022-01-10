<template>
  <div class="container page">
    <div class="row">
      <div class="col-sm-12">
        <h1>
          {{ name }}
        </h1>
      </div>
    </div>
    <Breadcrumbs 
      :crumbs="[{name: 'Авторы', url: '/user' }, {name, url: '' }]"
    />
    <div class="row">
      <div class="col-sm-2">
        <b-img 
          :src="`https://altertravel.ru/authors/${user.username}.jpg`"
          rounded
          fluid 
        />
        <Adsense
          data-ad-client="ca-pub-2550364618248551"
          data-ad-slot="3426016451"
          data-ad-format="auto"
        />
      </div>
      <div class="col-sm-10">
        <p><b>О себе</b></p>
        <p>{{ user.about }}</p>
        <p><b>Сайт</b></p>
        <p>{{ user.homepage }}</p>
        <h2>Публикации</h2>
        <b-button-group>
          <b-button
            :variant="sort === 'date' ? 'secondary' : 'outline-secondary'"
            @click="sort = 'date'"
          >
            Новые
          </b-button>
          <b-button
            :variant="sort === 'views' ? 'secondary' : 'outline-secondary'"
            @click="sort = 'views'"
          >
            Популярные
          </b-button>
          <b-button
            :variant="sort === 'comments' ? 'secondary' : 'outline-secondary'"
            @click="sort = 'comments'"
          >
            Комментируемые
          </b-button>
        </b-button-group>
        <Gallery :objects="pois" :loading="loadingPois"/>
        <b-pagination
          v-if="total / perPage > 1"
          v-model="page"
          :total-rows="total"
          :per-page="perPage"
          aria-controls="my-table"
        />
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data () {
    return {
      pois: [],
      loadingPois: false,
      total: 0,
      perPage: 12,
      page: 1,
      user: {
        name: null,
      },
      loadingUser: false,
      sort: 'date',
    }
  },
  head() {
    return {
      title: `Автор ${this.name} (${this.$route.params.id})`,
      meta: [
        {
          hid: 'description',
          name: 'description',
          content: `Автор ${this.name} (${this.$route.params.id})`,
        }
      ]
    }
  },
  watch: {
    page: {
      handler () {
        this.fetchPois()
      }
    },
    sort: {
      handler () {
        this.fetchPois()
      }
    }
  },
  computed: {
    name: {
      get: function () {
        return  this.user.name ? this.user.name : this.$route.params.id
      }
    }
  },
  async fetch () {
    await this.fetchUser()
    await this.fetchPois()
  },
  methods: {
    async fetchUser () {
      this.loadingUser = true
      const { data } = await axios.get('/users/' + this.$route.params.id)
      this.user = data.data
      this.loadingUser = false
    },
    async fetchPois () {
      this.loadingPois = true
      const { data } = await axios.get(
        '/pois', 
        { 
          params: {
            page: this.page, 
            limit: this.perPage, 
            sort: this.sort, 
            author: this.$route.params.id
          } 
        }
      )
      this.pois = data.data
      this.total = data.meta.total
      this.loadingPois = false
    },
  },
}
</script>
