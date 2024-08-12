<template>
  <div class="main-container">
    <TransitionGroup name="fade">
      <AmisRenderer
        :key="route.path"
        :amis-json="thisPage.pageJson"
        v-if="!thisPage.error && thisPage.pageJson && !thisPage.loading"
      />
      <ExceptionError
        :title="thisPage.errorMessage"
        :status="status"
        v-if="thisPage.error && !thisPage.loading"
      />
    </TransitionGroup>
  </div>
</template>

<script setup lang="ts">
const { thisPage } = storeToRefs(usePagesStore())
const { getPageJson } = usePagesStore()

const route = useRoute()
watch(
  () => route.path,
  async (path: string) => {
    await getPageJson(path)
  }
)
onMounted(async () => {
  await getPageJson(route.path)
})

const status = computed(() => {
  return thisPage.value.status.toString() as '403' | '404' | '500'
})
</script>

<style lang="less" scoped>
.main-container {
  padding: 0 10px 10px;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
