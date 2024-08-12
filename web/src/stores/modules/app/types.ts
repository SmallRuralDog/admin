import type { MenuItem } from '../user/types'

export interface AppState {
  theme: string
  colorWeak: boolean
  navbar: boolean
  menu: boolean
  topMenu: boolean
  hideMenu: boolean
  menuCollapse: boolean
  footer: boolean
  themeColor: string
  menuWidth: number
  globalSettings: boolean
  device: string
  tabBar: boolean
  menuFromServer: boolean
  serverMenu: MenuItem[]
  activeMenus: { [key: string]: string[] }
  config: AmisAdmin
  [key: string]: unknown
}

export interface AmisAdmin {
  apiBase: string
  prefix: string
  title: string
  logo: string
  loginBanner: {
    title: string
    desc: string
    image: string
  }[]
  loginTitle: string
  loginDesc: string
  footer: string
  captchaUrl: string
}
