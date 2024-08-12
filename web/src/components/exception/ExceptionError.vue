<template>
  <div class="content">
    <a-result class="result" :status="status" :subtitle="title"></a-result>
    <div class="operation-row">
      <a-button key="again" @click="retry" style="margin-right: 16px"> 重试 </a-button>
      <a-button key="back" type="primary" @click="goBack"> 返回 </a-button>
    </div>
  </div>
</template>

<script lang="ts" setup>
defineProps<{
  title: string
  status: '403' | '404' | '500'
}>()

//重试
const retry = () => {
  router.go(0)
}

const goBack = () => {
  router.back()
}
</script>

<style scoped lang="less">
.content {
  height: calc(100vh - 200px);
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  background-color: var(--color-bg-1);
  border-radius: 4px;
}
</style>
