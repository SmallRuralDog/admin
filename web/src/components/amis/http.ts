import ax, { type AxiosRequestConfig } from 'axios'

const axios = ax.create({
  timeout: 20 * 1000,
  maxBodyLength: 5 * 1024 * 1024,
  withCredentials: true
})

axios.interceptors.request.use(
  (config: AxiosRequestConfig | any) => {
    if (isLogin()) {
      config.headers = {
        ...config.headers,
        Authorization: `Bearer ${getToken()}`
      }
    }

    config.params = {
      ...config.params
    }
    return config
  },
  function (error) {
    return Promise.reject(error)
  }
)

axios.interceptors.response.use(
  (response) => {
    const { status } = response.data

    if (status !== 0) {
      if (status === 401) {
        useUserStore().logoutCallBack()
        return Promise.reject({ data: { status: 401, message: '未登录' } })
      }
      if (status === 404) {
        return Promise.reject({ data: { status: 404, message: '抱歉，页面不见了～' } })
      }
      if (status === 403) {
        return Promise.reject({ data: { status: 403, message: '对不起，您没有访问该资源的权限' } })
      }
      if (status === 500) {
        return Promise.reject({ data: { status: 500, message: '抱歉，服务器出了点问题～' } })
      }
      return Promise.reject(response.data)
    }

    return response
  },
  function (error) {
    if (error.response) {
      const { status } = error.response
      if (status === 401) {
        useUserStore().logoutCallBack()
        return Promise.reject({ data: { status: 401, message: '未登录' } })
      }
      if (status === 404) {
        return Promise.reject({ data: { status: 404, message: '抱歉，页面不见了～' } })
      }
      if (status === 403) {
        return Promise.reject({ data: { status: 403, message: '对不起，您没有访问该资源的权限' } })
      }
      if (status === 500) {
        return Promise.reject({ data: { status: 500, message: '抱歉，服务器出了点问题～' } })
      }
    }
    return Promise.reject(error.response)
  }
)

export const amisHttp = axios
