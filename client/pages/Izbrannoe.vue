<template>
  <div class="container page">
    <div class="row">
      <div class="col-sm-12">
        <h1 class="view">
          Маршут с избранными точками
        </h1>
      </div>
    </div>
    <Breadcrumbs :list="[{name: 'Избранное', url: '' }]" />
    <Map chosen />
  </div>
</template>

<script>
export default {

}
</script>

<style>

</style>