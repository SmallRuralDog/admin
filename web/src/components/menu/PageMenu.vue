<template>
  <a-menu
    v-if="menuInit"
    :mode="topMenu ? 'horizontal' : 'vertical'"
    v-model:collapsed="collapsed"
    v-model:open-keys="openKeys"
    :show-collapse-button="appStore.device !== 'mobile'"
    :auto-open="false"
    :selected-keys="selectedKey"
    :auto-open-selected="true"
    :level-indent="34"
    style="height: 100%; width: 100%"
    :onCollapse="setCollapse"
    class="select-none"
  >
    <template v-for="item in menuTree" :key="item.key">
      <MenuItem :item="item" :show-icon="true"></MenuItem>
    </template>
  </a-menu>
</template>

<script setup lang="ts">
import type { MenuItem } from '@/stores/modules/user/types'

const appStore = useAppStore()

//const { appActiveMenus } = storeToRefs(appStore)

const { menuTree } = useMenuTree()
const collapsed = computed({
  get() {
    if (appStore.device === 'desktop') return appStore.menuCollapse
    return false
  },
  set(value: boolean) {
    appStore.updateSettings({ menuCollapse: value })
  }
})
const route = useRoute()
const menuInit = ref(false)

onMounted(async () => {
  await appStore.fetchServerMenuConfig()
  menuInit.value = true

  getActiveMenu(selfMenu.value)

  const menu = useAppStore().getMenuItemByUri(route.path)

  menu && setRouteEmitter(menu)
})

const topMenu = computed(() => appStore.topMenu)
const openKeys = useStorage<string[]>('admin-open-keys', [])
const selectedKey = ref<string[]>([])

const findMenuOpenKeys = (target: string) => {
  const result: string[] = []
  let isFind = false
  const backtrack = (item: MenuItem, keys: string[]) => {
    if (item.id === target) {
      isFind = true
      result.push(...keys)
      return
    }
    if (item.children?.length) {
      item.children.forEach((el) => {
        backtrack(el, [...keys, el.id as string])
      })
    }
  }
  menuTree.value.forEach((el: MenuItem) => {
    if (isFind) return // Performance optimization
    backtrack(el, [el.id as string])
  })
  return result
}

const setCollapse = (val: boolean) => {
  if (appStore.device === 'desktop') appStore.updateSettings({ menuCollapse: val })
}
watch(
  () => route.path,
  () => {
    getActiveMenu(selfMenu.value)
  }
)
const getActiveMenu = (menu: MenuItem) => {
  if (!menu) return
  if (!menu.show && menu.active_menu) {
    selectedKey.value = [menu.active_menu]
  } else {
    selectedKey.value = [menu.id]
  }

  updateOpenKeys(findMenuOpenKeys(menu.id))
}

const updateOpenKeys = (keys: string[]) => {
  keys.forEach((key) => {
    if (!openKeys.value.includes(key)) {
      openKeys.value.push(key)
    }
  })
}

const selfMenu = computed<MenuItem>(() => {
  return useAppStore().getMenuItemByUri(route.path)
})
</script>

<style lang="less" scoped>
:deep(.arco-menu-inner) {
  .arco-menu-inline-header {
    display: flex;
    align-items: center;
  }
  .arco-icon {
    &:not(.arco-icon-down) {
      font-size: 18px;
    }
  }
}
</style>
