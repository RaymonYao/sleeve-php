import Vue from 'vue'
if (document.body.clientWidth < 768) {
    Vue.prototype.$ismobile = true
} else {
    Vue.prototype.$ismobile = false
}
