export type RoleType = '' | '*' | 'admin' | 'user'
export interface UserState {
  name?: string
  avatar?: string
  job?: string
  organization?: string
  location?: string
  email?: string
  introduction?: string
  personalWebsite?: string
  jobName?: string
  organizationName?: string
  locationName?: string
  phone?: string
  registrationDate?: string
  accountId?: string
  certification?: number
  role: RoleType
}

export interface MenuItem {
  id: string
  path: string
  name: string
  icon: string
  parent_id: number
  show: boolean
  is_ext: boolean
  ext_open_mode: number
  active_menu: string
  params: {
    [key: string]: any
  }
  children?: MenuItem[]
}
