const state = () => {
  countries: []
}

const mutations = {
  setCountrues(state, countries) {
    state.countries = countries
  }
}

const getters = {
  getCountrues: (state) => state.countries,
}

export default {
  state,
  mutations,
  getters
}