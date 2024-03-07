import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export const store = new Vuex.Store({
    state: {
        corpId: 'ding69f5cd88b97e811735c2f4657eb6378f',
        userInfo: {}
    },
    mutations: {
      setUserinfo(state, payload) {
        state.userInfo = payload
      },
    },
    actions: {},
    modules: {}
})
