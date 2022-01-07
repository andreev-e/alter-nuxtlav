<template>
  <div class="container page">
    <Header />
    <Breadcrumbs 
      :crumbs="[{name: 'Каталог', url: '' }]"
    />
    <b-row>
      <b-tabs pills card vertical>
        <b-tab title="Все">
          <Gallery :objects="pois" />
        </b-tab>
        <b-tab title="Природные"></b-tab>
        <b-tab title="Техногенные"></b-tab>
        <b-tab title="Культурные"></b-tab>
        <b-tab title="Архитектурные"></b-tab>
        <b-tab title="Музеи"></b-tab>
        <b-tab title="Памятники"></b-tab>
      </b-tabs>
    </b-row>
  </div>
</template>

<script>
export default {
  data() {
    return {
      pois: [],
    }
  }
}
</script>

<style>

</style>