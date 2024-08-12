<template>
  <div class="navbar">
    <div class="left-side">
      <a-space>
        <icon-menu-fold
          v-if="!topMenu && appStore.device === 'mobile'"
          style="font-size: 22px; cursor: pointer"
          @click="toggleDrawerMenu"
        />
      </a-space>
    </div>
    <ul class="right-side">
      <li>
        <a-tooltip :content="theme === 'light' ? '点击切换为暗黑模式' : '点击切换为亮色模式'">
          <a-button class="nav-btn" type="outline" :shape="'circle'" @click="handleToggleTheme">
            <template #icon>
              <icon-moon-fill v-if="theme === 'dark'" />
              <icon-sun-fill v-else />
            </template>
          </a-button>
        </a-tooltip>
      </li>
      <li>
        <a-tooltip :content="isFullscreen ? '点击退出全屏模式' : '点击切换全屏模式'">
          <a-button class="nav-btn" type="outline" :shape="'circle'" @click="toggleFullScreen">
            <template #icon>
              <icon-fullscreen-exit v-if="isFullscreen" />
              <icon-fullscreen v-else />
            </template>
          </a-button>
        </a-tooltip>
      </li>
      <li>
        <a-dropdown trigger="click">
          <a-avatar :size="32" :style="{ marginRight: '8px', cursor: 'pointer' }">
            <img v-if="avatar" alt="avatar" :src="avatar" />
            <IconUser v-else />
          </a-avatar>
          <template #content>
            <a-doption>
              <a-space @click="$router.push('/userSetting')">
                <icon-settings />
                <span> 用户设置 </span>
              </a-space>
            </a-doption>
            <a-doption>
              <a-space @click="handleLogout">
                <icon-export />
                <span> 退出登录 </span>
              </a-space>
            </a-doption>
          </template>
        </a-dropdown>
      </li>
    </ul>
  </div>
</template>

<script lang="ts" setup>
import { useDark, useToggle, useFullscreen } from '@vueuse/core'

const appStore = useAppStore()
const userStore = useUserStore()
const { isFullscreen, toggle: toggleFullScreen } = useFullscreen()
const avatar = computed(() => {
  return userStore.avatar
})
const theme = computed(() => {
  return appStore.theme
})

onMounted(() => {})

const topMenu = computed(() => appStore.topMenu && appStore.menu)
const isDark = useDark({
  selector: 'body',
  attribute: 'arco-theme',
  valueDark: 'dark',
  valueLight: 'light',
  storageKey: 'arco-theme',
  onChanged(dark: boolean) {
    appStore.toggleTheme(dark)
  }
})
const toggleTheme = useToggle(isDark)
const handleToggleTheme = () => {
  toggleTheme()
  location.reload()
}

const handleLogout = () => {
  userStore.logout()
}
const toggleDrawerMenu = inject('toggleDrawerMenu') as () => void
</script>

<style scoped lang="less">
.navbar {
  display: flex;
  justify-content: space-between;
  height: 100%;
  background-color: var(--color-bg-2);
  border-bottom: 1px solid var(--color-border);
  position: fixed;
  top: 0;
  right: 0;
  left: 0;
  height: 60px;
  z-index: 998;
}

.left-side {
  display: flex;
  align-items: center;
  padding-left: 20px;
}

.right-side {
  display: flex;
  padding-right: 20px;
  list-style: none;

  :deep(.locale-select) {
    border-radius: 20px;
  }

  li {
    display: flex;
    align-items: center;
    padding: 0 10px;
  }

  a {
    color: var(--color-text-1);
    text-decoration: none;
  }

  .nav-btn {
    border-color: rgb(var(--gray-2));
    color: rgb(var(--gray-8));
    font-size: 16px;
  }

  .trigger-btn,
  .ref-btn {
    position: absolute;
    bottom: 14px;
  }

  .trigger-btn {
    margin-left: 14px;
  }
}
</style>

<style lang="less">
.message-popover {
  .arco-popover-content {
    margin-top: 0;
  }
}

.arco-dropdown-list-wrapper {
  max-height: 100vh !important;
}
</style>
