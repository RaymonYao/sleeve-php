import Vue from 'vue'
import VueRouter from 'vue-router'
import List from '../views/list.vue'
import Detail from '../views/detail.vue'

Vue.use(VueRouter)

const routes = [
    {
        path: '/',
        name: 'List',
        component: List
    },
    {
      path: '/detail',
      name: 'Detail',
      component: Detail
    }
]

const router = new VueRouter({
    routes
})

export default router
