<template>
  <div class="row p-0">
    <div class="col-12 text-center m-3"><b-spinner v-if="loading" label="Spinning" /></div>
    <div class="col-12">
      <b-carousel
        controls
        indicators
        :interval="10000"
      >
        <b-carousel-slide
          v-for="(comment) in comments"
          :key="comment.commentid"
          style="min-height:250px;"
        >
          <b-row class="comment">
            <b-col cols="3">
              <router-link :to="'/user/' + comment.name">
                <p>{{ comment.name }}</p>
              </router-link>
              <p><i>{{ comment.date }}</i></p>
              <router-link v-if="!id" :to="'/poi/' + comment.object.id">
                <p>&laquo;<span v-html="comment.object.name" />&raquo;</p>
              </router-link>
            </b-col>
            <b-col cols="9" class="comment-text">
             <p>{{ comment.comment }}</p>
            </b-col>
          </b-row>
        </b-carousel-slide>
      </b-carousel>
      <b-form v-if="id">
        <b-form-group>
          <b-form-textarea
            id="text"
            v-model="form.text"
            placeholder="Текст комментария"
            required
            rows="3"
            max-rows="6"
          />
        </b-form-group>
        <b-button type="submit" variant="primary" @click="postComment">
          Отправить
        </b-button>
      </b-form>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'CommentsCarousel',
  props: {
    id: {
      type: [Number, String],
      required: false,
      default: null
    },
    type: {
      type: String,
      required: false,
      default: null
    }
  },
  data () {
    return {
      comments: [],
      loading: true,
      form: {
        text: null
      }
    }
  },
  async fetch () {
    await this.fetchComments()
  },
  // mounted () {
  //   this.fetchComments()
  // },
  methods: {
    async fetchComments () {
      this.loading = true
      const { data } = await axios.get(
        '/comments',
        { params: { id: this.id, type: this.type } }
      )
      this.comments = data.data
      this.loading = false
    },
    async postComment () {
      const { data } = await axios.post(
        '/comments',
        { text: this.form.text } 
      )
      console.log(data);
    }
  }
}
</script>

<style>
  .comment {
    min-height: 100px;
  }
  .comment p{
    color:#000;
    text-align: left;
  }
</style>
