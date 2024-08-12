import type { AppState } from './types'
import { Notification } from '@arco-design/web-vue'
import defaultSettings from '@/config/settings.json'
import type { MenuItem } from '../user/types'
import type { ApiError } from '@/api/http'

import { useCookies } from '@vueuse/integrations/useCookies'

const { set } = useCookies()

const useAppStore = defineStore('app', {
  state: (): AppState => ({ ...defaultSettings, config: window.AmisAdmin }),
  getters: {
    appCurrentSetting(state: AppState): AppState {
      return { ...state }
    },
    appDevice(state: AppState) {
      return state.device
    },
    appAsyncMenus(state: AppState): MenuItem[] {
      return state.serverMenu as unknown as MenuItem[]
    },
    appActiveMenus(state: AppState) {
      return state.activeMenus
    }
  },
  actions: {
    // Update app settings
    updateSettings(partial: Partial<AppState>) {
      // @ts-ignore-next-line
      this.$patch(partial)
    },

    // Change theme color
    toggleTheme(dark: boolean) {
      if (dark) {
        this.theme = 'dark'
        document.body.setAttribute('arco-theme', 'dark')
        set('arco-theme', 'dark', {
          path: '/'
        })
      } else {
        this.theme = 'light'
        document.body.removeAttribute('arco-theme')
        set('arco-theme', 'light', {
          path: '/'
        })
      }
    },
    toggleDevice(device: string) {
      this.device = device
    },
    toggleMenu(value: boolean) {
      this.hideMenu = value
    },
    async fetchServerMenuConfig() {
      try {
        const { data } = await getMenuList()
        this.serverMenu = data.menus
        this.activeMenus = data.active_menus
      } catch (error) {
        Notification.error({
          title: '错误',
          content: (error as ApiError).message
        })
      }
    },
    clearServerMenu() {
      this.serverMenu = []
    },
    getMenuItemByUri(uri: string): MenuItem {
      //以？分割字符串，取第一个
      uri = uri.split('?')[0]

      const result: MenuItem[] = []
      let isFind = false
      const backtrack = (item: MenuItem) => {
        if (item.path == uri) {
          isFind = true
          result.push(item)
          return
        }
        if (item.children?.length) {
          item.children.forEach((el) => {
            backtrack(el)
          })
        }
      }

      this.serverMenu.forEach((item: MenuItem) => {
        if (isFind) return // Performance optimization
        backtrack(item)
      })

      return result[0]
    }
  }
})

export default useAppStore
