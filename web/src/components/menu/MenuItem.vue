<template>
  <template v-if="item.children && item.children.length > 0">
    <a-sub-menu :key="item.id">
      <template #icon>
        <i class="text-15" :class="item.icon"></i>
      </template>
      <template #title>
        <span>{{ item.name }}</span>
      </template>

      <template v-for="child in item.children" :key="child.key">
        <MenuItem :item="child" :show-icon="false"></MenuItem>
      </template>
    </a-sub-menu>
  </template>
  <template v-else>
    <a-menu-item :key="item.id" @click="itemClick(item)">
      <template v-if="item.icon && showIcon" #icon>
        <i class="text-15" :class="item.icon"></i>
      </template>
      <span>{{ item.name }}</span>
    </a-menu-item>
  </template>
</template>

<script setup lang="ts">
import type { MenuItem } from '@/stores/modules/user/types'

defineProps<{
  item: MenuItem
  showIcon: boolean
}>()

const route = useRoute()

const itemClick = (item: MenuItem) => {
  if (item.is_ext) {
    const a = document.createElement('a')
    if (item.ext_open_mode == 2) {
      a.target = '_blank'
    }
    a.href = item.path
    a.click()
    return
  }

  if (route.path === item.path) {
    return
  }

  router.push({ path: item.path, query: item.params })
}
</script>
