const state = {
  h1: '',
  breadcrumbs: [],
}

const mutations = {
  setH1(state, h1) {
    state.h1 = h1
  },
  setBreadcrumbs(state, breadcrumbs) {
    state.breadcrumbs = breadcrumbs
  }
}

const getters = {
  getH1: (state) => state.h1,
  getBreadcrumbs: (state) => state.breadcrumbs,
}

export default {
  state,
  mutations,
  getters
}