<template>
  <a-breadcrumb class="container-breadcrumb">
    <a-breadcrumb-item>
      <icon-apps />
    </a-breadcrumb-item>
    <a-breadcrumb-item v-for="item in pageBreadcrumb" :key="item">
      {{ item }}
    </a-breadcrumb-item>
  </a-breadcrumb>
</template>

<script lang="ts" setup>
const { pageBreadcrumb } = storeToRefs(usePagesStore())
</script>

<style scoped lang="less">
.container-breadcrumb {
  margin: 10px 0;
  :deep(.arco-breadcrumb-item) {
    color: rgb(var(--gray-6));
    &:last-child {
      color: rgb(var(--gray-8));
    }
  }
}
</style>
