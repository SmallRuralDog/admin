import type { MenuItem } from '../user/types'
import type { TabBarState, TagProps } from './types'

const formatTag = (route: MenuItem): TagProps => {
  const { name, id, path, params } = route
  return {
    name: name || '',
    id: String(id),
    path,
    params
  }
}

const useAppStore = defineStore('tabBar', {
  state: (): TabBarState => ({
    cacheTabList: new Set(['home']),
    tagList: [
      {
        name: '工作台',
        id: 'home',
        path: '/home',
        params: {}
      }
    ]
  }),
  getters: {
    getTabList(): TagProps[] {
      return this.tagList
    },
    getCacheList(): string[] {
      return Array.from(this.cacheTabList)
    }
  },
  actions: {
    updateTabList(route: MenuItem) {
      this.tagList.push(formatTag(route))
      this.cacheTabList.add(route.id as string)
    },
    deleteTag(idx: number, tag: TagProps) {
      this.tagList.splice(idx, 1)
      this.cacheTabList.delete(tag.name)
    },
    addCache(name: string) {
      if (isString(name) && name !== '') this.cacheTabList.add(name)
    },
    deleteCache(tag: TagProps) {
      this.cacheTabList.delete(tag.name)
    },
    freshTabList(tags: TagProps[]) {
      this.tagList = tags
      this.cacheTabList.clear()
      // 要先判断 ignoreCache
      this.tagList
        .filter((el) => !el.ignoreCache)
        .map((el) => el.name)
        .forEach((x) => this.cacheTabList.add(x))
    },
    resetTabList() {
      this.tagList = []
      this.cacheTabList.clear()
      this.cacheTabList.add('home')
    }
  }
})

export default useAppStore
