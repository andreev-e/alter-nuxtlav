<template>
  <div class="row">
    <div class="col-12 text-center m-3"><b-spinner v-if="loading" label="Spinning" /></div>
    <div class="col-sm-12">
      <div id="comments_list" class="comments-list">
        <div v-for="(comment, index) in comments" :key="comment.commentid" class="comment">
          <div class="author-date">
            <b>
              <router-link :to="'/user/' + comment.name">
                {{ comment.name }}
              </router-link>
            </b>
            <i>{{ comment.date }} {{ comment.time }}</i>
            <router-link v-if="!id" :to="'/poi/' + comment.object.id">
              об объекте
              <span v-html="comment.object.name" />
            </router-link>
          </div>
          <div class="comment-text-wrapper">
            <div class="comment-text">
              <p>{{ comment.comment }}</p>
            </div>
          </div>
          <hr v-if="index != comments.length - 1">
        </div>
      </div>
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
  name: 'Comments',
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

</style>
