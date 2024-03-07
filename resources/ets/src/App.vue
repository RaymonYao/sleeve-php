<template>
  <div v-if="getUser" id="app">
    <router-view/>
  </div>
</template>

<script>
import * as dd from 'dingtalk-jsapi';
import {login} from "./api/api";
import {store} from '@/store'

export default {
  metaInfo: {
    meta: [
      {name: 'viewport', content: 'width=device-width,user-scalable=no,minimum-scale=1.0,maximum-scale=1.0'}
    ]
  },
  data() {
    return {
      code_info: {},
      resp: {},
      getUser: false
    }
  },
  async created() {
    if (process.env.NODE_ENV === 'development') {
      const res = await login("04205142441145640", 1)
      store.commit("setUserinfo", res.data.data)
      this.getUser = true
    }
    if (process.env.NODE_ENV === 'production') {
      if (dd.env.platform !== "notInDingTalk") {
        dd.ready(() => {
          dd.runtime.permission.requestAuthCode({
            corpId: this.$store.state.corpId,
            onSuccess: async (info) => {
              const res = await login(info.code, 0)
              store.commit("setUserinfo", res.data.data)
              this.getUser = true
            }
          });
        });
      } else {
        document.write("Please open in DingTalk!");
      }
    }
  }
}
</script>
<style lang="scss">
.test1 {
  p {
    color: #ff0000;
    display: flex;
  }
}
</style>