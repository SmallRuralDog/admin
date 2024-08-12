import type { MenuItem } from '@/stores/modules/user/types'
import { cloneDeep } from 'lodash'

export const useMenuTree = () => {
  const appStore = useAppStore()
  const appRoute = computed(() => {
    if (appStore.menuFromServer) {
      return appStore.appAsyncMenus
    }
    return []
  })
  const menuTree = computed<MenuItem[]>(() => {
    const copyRouter = cloneDeep(appRoute.value) as MenuItem[]

    function travel(_routes: MenuItem[], layer: number) {
      if (!_routes) return null

      const collector: any = _routes.map((element) => {
        // leaf node
        if (!element.children) {
          element.children = []
          return element
        }

        // route filter hideInMenu true
        element.children = element.children.filter((x) => x.show == true)

        // Associated child node
        const subItem = travel(element.children, layer + 1)

        if (subItem.length) {
          element.children = subItem
          return element
        }
        // the else logic
        if (layer > 1) {
          element.children = subItem
          return element
        }

        if (element.show) {
          return element
        }

        return null
      })
      return collector.filter(Boolean)
    }
    return travel(copyRouter, 0)
  })

  return {
    menuTree
  }
}
