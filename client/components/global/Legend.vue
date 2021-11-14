<template>
  <div class="row nopadding">
    <div class="legend-title col-sm-12">
      <b>Легенда карты и фильтры</b>
      <div style="display:flex; align-items:center; justify-content: space-around">
        <b-form-checkbox-group
          v-model="selected"
          :options="types"
          @change="updateValue"
        />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Legend',
  props: {
    value: {
      type: Array,
    }
  },
  data() {
    return {
      types: [
        'Архитектура',
        'История/Культура',
        'Техноген',
        'Памятник',
        'Музей',
        'Ночлег',
        'Еда',
        'Покупки'
      ],
      selected: [],
    }
  },
  created() {
    if (this.value.length > 0) {
      this.selected = this.value;
    } else {
      this.selected = this.types;
      this.$emit('input', this.selected)
    }
  },
  methods: {
    updateValue: function (val) {
      this.$emit('input', val)
    }
  },
};
</script>

<style scoped>
  .legend-title {
    color: #fff;
    background: #7495AA;
    margin: 0 auto;
    text-align: center;
    vertical-align: middle;
    padding: 3px 0;
    position: relative;
    z-index: 0;
    text-decoration: none;
    cursor: pointer;
  }
  
</style>