<template>
  <div class="amis-main" ref="amisEL"></div>
</template>

<script setup lang="ts">
//@ts-ignore
const amis = window.amisRequire('amis/embed')
const amisEL = ref(null)

const props = defineProps<{
  amisJson: object
}>()

const router = useRouter()
const route = useRoute()

const amisScoped = ref(null)

const build = () => {
  amisScoped.value = amis.embed(
    amisEL.value,
    props.amisJson,
    {},
    {
      fetcher: ({
        url, // 接口地址
        method, // 请求方法 get、post、put、delete
        data, // 请求数据
        responseType,
        config, // 其他配置
        headers // 请求头
      }: any) => {
        config = config || {}
        config.withCredentials = true
        responseType && (config.responseType = responseType)

        config.headers = headers || {}

        if (method !== 'post' && method !== 'put' && method !== 'patch') {
          if (data) {
            config.params = data
          }
          return (amisHttp as any)[method](url, config)
          //@ts-ignore
        } else if (data && data instanceof FormData) {
          config.headers = config.headers || {}
          config.headers['Content-Type'] = 'multipart/form-data'
        } else if (
          data &&
          typeof data !== 'string' &&
          //@ts-ignore
          !(data instanceof Blob) &&
          !(data instanceof ArrayBuffer)
        ) {
          data = JSON.stringify(data)
          config.headers = config.headers || {}
          config.headers['Content-Type'] = 'application/json'
        }
        return (amisHttp as any)[method](url, data, config)
      },
      jumpTo: (to: string) => {
        if (to == 'back()') {
          router.back()
          return
        }
        //检测 http://或者 https://
        if (to.indexOf('http://') == 0 || to.indexOf('https://') == 0) {
          //@ts-ignore
          window.location.href = to
          return
        }
        router.push(to)
      },
      updateLocation: (to: string, replace: boolean) => {
        //过滤 query 参数
        const queryArr = to.split('&').filter((item) => {
          return item.indexOf('search[') === -1
        })
        let query = queryArr.join('&')
        //判断是否？开头
        if (query.indexOf('?') !== 0) {
          query = '?' + query
        }
        const path = route.path + query
        if (replace) {
          router.replace(path)
        } else {
          router.push(path)
        }
      },
      affixOffsetTop: 48
    }
  )
}

onMounted(() => build())
onUnmounted(() => {
  // @ts-ignore
  amisScoped.value?.unmount()
})
</script>

<style lang="less">
.amis-main {
  position: relative;
  height: calc(100vh - 147px);
}
.amis-scope {
  background-color: transparent;
  .cxd-Page {
    border-radius: 4px;
  }
}
</style>
