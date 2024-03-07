import axios from 'axios'
import {store} from '@/store'
import {Loading, Message} from "element-ui"
import * as dd from 'dingtalk-jsapi';

let number = 0;

const loadingHtml = document.createElement('div')
loadingHtml.className = 'pageloading'
loadingHtml.innerHTML = '<i class="el-icon-loading"/>'

const service = axios.create({
  baseURL: process.env.VUE_APP_TK_BASE_API,
  timeout: process.env.VUE_APP_TK_TIMEOUT //毫秒
});

service.interceptors.request.use(async (config) => {
  const _token = store.state.userInfo.accessToken
  if (_token !== undefined) {
    const nowTimestamp = Date.parse(new Date()) / 1000;
    const expTimestamp = JSON.parse(atob(_token.split('.')[1])).exp;
    if (nowTimestamp > expTimestamp - 60 && !config.url.startsWith('/api/login')) {
      if (dd.env.platform !== "notInDingTalk") {
        await refreshToken()
      }
    }
  }
  config.headers = {
    'accessToken': store.state.userInfo.accessToken
  }
  number += 1;
  if (number === 1) {
    document.body.append(loadingHtml);
  }
  return config
}, (error) => {
  number -= 1;
  if (number === 0) {
    document.body.removeChild(loadingHtml);
  }
  Message({
    message: error,
    type: "error",
  })
  return Promise.reject(error);
});

service.interceptors.response.use((resp) => {
  number -= 1;
  if (number === 0) {
    document.body.removeChild(loadingHtml);
  }
  if (resp.data.code !== 100) {
    Message({
      message: resp.data.msg,
      type: "error",
    })
  }
  return resp
}, (error) => {
  number -= 1;
  if (number === 0) {
    document.body.removeChild(loadingHtml);
  }
  Message({
    message: error,
    type: "error",
  })
  return Promise.reject(error)
});

async function refreshToken() {
  const codeInfo = await dd.runtime.permission.requestAuthCode({corpId: store.state.corpId});
  const _resp = await service({
    url: '/api/login/' + codeInfo.code,
    method: 'get',
    params: {
      env: 0
    }
  });
  store.commit('setUserinfo', _resp.data.data)
}

export default service;