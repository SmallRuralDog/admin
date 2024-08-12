<template>
  <div class="container">
    <div class="logo">
      <img alt="logo" class="h-33 w-33" :src="amisAdmin.logo" />
      <div class="logo-text">{{ amisAdmin.title }}</div>
    </div>
    <LoginBanner />
    <div class="content">
      <div class="content-inner">
        <LoginForm />
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import LoginBanner from './components/LoginBanner.vue'
import LoginForm from './components/LoginForm.vue'

const amisAdmin = ref(window.AmisAdmin)

localStorage.setItem('arco-theme', 'light')
</script>

<style lang="less" scoped>
.container {
  display: flex;
  height: 500px;
  border: 1px solid #eee;
  width: 900px;
  border-radius: 10px;
  overflow: hidden;
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translateX(-50%) translateY(-50%);

  .banner {
    width: 400px;
    background: linear-gradient(163.85deg, #1d2129 0%, #00308f 100%);
  }

  .content {
    border-radius: 10px;
    position: relative;
    display: flex;
    flex: 1;
    align-items: center;
    justify-content: center;
    padding-bottom: 40px;
  }
}

.logo {
  position: fixed;
  top: 24px;
  left: 22px;
  z-index: 1;
  display: inline-flex;
  align-items: center;

  &-text {
    margin-right: 4px;
    margin-left: 4px;
    color: var(--color-fill-1);
    font-size: 20px;
  }
}
</style>
