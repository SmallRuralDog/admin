<template>
  <a-dropdown trigger="contextMenu" :popup-max-height="false" @select="actionSelect">
    <a-tag
      closable
      checkable
      :checked="itemData.path === $route.path"
      @check="goto(itemData)"
      @close="tagClose(itemData, index)"
    >
      <span class="tag-link">
        {{ itemData.name }}
      </span>
    </a-tag>
    <template #content>
      <a-doption :disabled="disabledReload" :value="Eaction.reload">
        <icon-refresh />
        <span>重新加载</span>
      </a-doption>
      <a-doption class="sperate-line" :disabled="disabledCurrent" :value="Eaction.current">
        <icon-close />
        <span>关闭当前标签页</span>
      </a-doption>
      <a-doption :disabled="disabledLeft" :value="Eaction.left">
        <icon-to-left />
        <span>关闭左侧标签页</span>
      </a-doption>
      <a-doption class="sperate-line" :disabled="disabledRight" :value="Eaction.right">
        <icon-to-right />
        <span>关闭右侧标签页</span>
      </a-doption>
      <a-doption :value="Eaction.others">
        <icon-swap />
        <span>关闭其它标签页</span>
      </a-doption>
    </template>
  </a-dropdown>
</template>

<script lang="ts" setup>
import type { TagProps } from '@/stores/modules/tab-bar/types'

// eslint-disable-next-line no-shadow
enum Eaction {
  reload = 'reload',
  current = 'current',
  left = 'left',
  right = 'right',
  others = 'others'
}

const props = defineProps({
  itemData: {
    type: Object as PropType<TagProps>,
    default() {
      return []
    }
  },
  index: {
    type: Number,
    default: 0
  }
})

const router = useRouter()
const route = useRoute()
const tabBarStore = useTabBarStore()

const goto = (tag: TagProps) => {
  router.push({ path: tag.path, query: tag.params })
}
const tagList = computed(() => {
  return tabBarStore.getTabList
})

const disabledReload = computed(() => {
  return props.itemData.path !== route.path
})

const disabledCurrent = computed(() => {
  return props.index === 0
})

const disabledLeft = computed(() => {
  return [0, 1].includes(props.index)
})

const disabledRight = computed(() => {
  return props.index === tagList.value.length - 1
})

const tagClose = (tag: TagProps, idx: number) => {
  tabBarStore.deleteTag(idx, tag)
  if (props.itemData.path === route.path) {
    const latest = tagList.value[idx - 1] // 获取队列的前一个 tab

    router.push({ path: latest.path, query: latest.params })
  }
}

const findCurrentRouteIndex = () => {
  return tagList.value.findIndex((el) => el.path === route.path)
}
const actionSelect = async (value: any) => {
  const { itemData, index } = props
  const copyTagList = [...tagList.value]
  if (value === Eaction.current) {
    tagClose(itemData, index)
  } else if (value === Eaction.left) {
    const currentRouteIdx = findCurrentRouteIndex()
    copyTagList.splice(1, props.index - 1)

    tabBarStore.freshTabList(copyTagList)
    if (currentRouteIdx < index) {
      router.push({ name: itemData.name })
    }
  } else if (value === Eaction.right) {
    const currentRouteIdx = findCurrentRouteIndex()
    copyTagList.splice(props.index + 1)

    tabBarStore.freshTabList(copyTagList)
    if (currentRouteIdx > index) {
      router.push({ name: itemData.name })
    }
  } else if (value === Eaction.others) {
    const filterList = tagList.value.filter((el, idx) => {
      return idx === 0 || idx === props.index
    })
    tabBarStore.freshTabList(filterList)
    router.push({ name: itemData.name })
  } else if (value === Eaction.reload) {
    location.reload()
  }
}
</script>

<style scoped lang="less">
.tag-link {
  color: var(--color-text-2);
  text-decoration: none;
}
.link-activated {
  color: rgb(var(--link-6));
  .tag-link {
    color: rgb(var(--link-6));
  }
  & + .arco-tag-close-btn {
    color: rgb(var(--link-6));
  }
}
:deep(.arco-dropdown-option-content) {
  span {
    margin-left: 10px;
  }
}
.arco-dropdown-open {
  .tag-link {
    color: rgb(var(--danger-6));
  }
  .arco-tag-close-btn {
    color: rgb(var(--danger-6));
  }
}
.sperate-line {
  border-bottom: 1px solid var(--color-neutral-3);
}
</style>
