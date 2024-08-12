import ax, { type AxiosRequestConfig } from 'axios'

export interface ApiError {
  status: number
  message: string
}

export interface ResType<T> {
  status: number
  data: T
  message?: string
  action?: 'jump' | 'toast' | 'renderPage'
  actionType?: 'url' | 'route'
  url?: string
  showMenu?: boolean
  showHeader?: boolean
}

const config = window.AmisAdmin

const axios = ax.create({
  baseURL: config.apiBase,
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

// 添加响应拦截器
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

interface Http {
  get<T>(url: string, params?: unknown): Promise<T>

  post<T>(url: string, params?: unknown): Promise<T>

  upload<T>(url: string, params: unknown): Promise<T>

  put<T>(url: string, params: unknown): Promise<T>

  delete<T>(url: string, params: unknown): Promise<T>

  download(url: string): void
}

const http: Http = {
  get(url, params) {
    return new Promise((resolve, reject) => {
      axios
        .get(url, { params })
        .then((res) => {
          resolve(res.data)
        })
        .catch((err) => {
          reject(err.data)
        })
    })
  },
  post(url, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(url, params, {})
        .then((res) => {
          resolve(res.data)
        })
        .catch((err) => {
          reject(err.data)
        })
    })
  },

  put(url, params) {
    return new Promise((resolve, reject) => {
      axios
        .put(url, params)
        .then((res) => {
          resolve(res.data)
        })
        .catch((err) => {
          reject(err.data)
        })
    })
  },

  delete(url, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(url, { params })
        .then((res) => {
          resolve(res.data)
        })
        .catch((err) => {
          reject(err.data)
        })
    })
  },

  upload(url, file) {
    return new Promise((resolve, reject) => {
      axios
        .post(url, file, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
        .then((res) => {
          resolve(res.data)
        })
        .catch((err) => {
          reject(err.data)
        })
    })
  },

  download(url) {
    const iframe = document.createElement('iframe')
    iframe.style.display = 'none'
    iframe.src = url
    iframe.onload = function () {
      document.body.removeChild(iframe)
    }

    document.body.appendChild(iframe)
  }
}

export default http
