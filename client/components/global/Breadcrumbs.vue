<template>
  <div v-if="crumbs.length" class="content row">
    <div class="navigation col-12">
      <ul>
        <li class="first">
          <router-link to="/">
            <b-icon icon="house-door"></b-icon>
          </router-link>
        </li>
        <li
          v-for="(crumb, index) in crumbs"
          :key="crumb.id"
        >
          <router-link v-if="crumb.url" :to="crumb.url">
            {{ crumb.name }}
          </router-link>
          <span v-else>
            {{ crumb.name }}
          </span>
        </li>
        <li v-if="loading">
          <span>
            <b-spinner small />
          </span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Breadcrumbs',
  props: {
    crumbs: {
      type: Array,
      required: false,
      default: () => []
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
}
</script>

<style>
  .navigation li {
      list-style-image: url('https://altertravel.ru/i/list_style.png');
      float: left;
      margin-left: 24px;
      font-size: 17px;
  }
  .navigation a {
    color: #27475c;
  }
  .navigation li.first {
    list-style-image: none;
    margin-left: 0;
  }
</style>
