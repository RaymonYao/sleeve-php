require('./common');

import Vue from 'vue'
import App from './App.vue'
import router from './router'
import {store} from './store'
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import './css/common.scss'
import VueQuillEditor from 'vue-quill-editor'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'
import VueMeta from 'vue-meta'
Vue.use(VueMeta)
Vue.config.productionTip = false
Vue.use(ElementUI);
Vue.use(VueQuillEditor)

new Vue({
    router,
    store,
    render: h => h(App)
}).$mount('#app')

