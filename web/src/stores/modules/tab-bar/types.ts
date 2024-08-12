export interface TagProps {
  id: string
  name: string
  path: string
  params?: any
  ignoreCache?: boolean
}

export interface TabBarState {
  tagList: TagProps[]
  cacheTabList: Set<string>
}
