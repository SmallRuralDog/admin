import type { MenuItem } from '@/stores/modules/user/types'
import type { ResType } from './http'

export interface LoginData {
  username: string
  password: string
}

export interface LoginRes {
  token_type: string
  expires_in: number
  access_token: string
  refresh_token: string
}

export function userLogin(data: LoginData) {
  return http.post<ResType<LoginRes>>('/auth/login', data)
}

export function userLogout() {
  return http.post('/auth/logout')
}

export function getMenuList() {
  return http.get<
    ResType<{
      active_menus: { [key: string]: string[] }
      menus: MenuItem[]
    }>
  >('/userMenus')
}

export async function getPageRenderer(path: string) {
  return await http.get<ResType<any>>(path)
}
