<template>
  <div class="login-form-wrapper">
    <div class="login-form-title">{{ config.loginTitle }}</div>
    <div class="login-form-sub-title">{{ config.loginDesc }}</div>
    <div class="login-form-error-msg">{{ errorMessage }}</div>
    <a-form
      ref="loginForm"
      :model="userInfo"
      class="login-form"
      layout="vertical"
      @submit="handleSubmit"
    >
      <a-form-item
        field="username"
        :rules="[{ required: true, message: '用户名不能为空' }]"
        :validate-trigger="['change', 'blur']"
        hide-label
      >
        <a-input v-model="userInfo.username" :placeholder="`用户名`">
          <template #prefix>
            <icon-user />
          </template>
        </a-input>
      </a-form-item>
      <a-form-item
        field="password"
        :rules="[{ required: true, message: `密码不能为空` }]"
        :validate-trigger="['change', 'blur']"
        hide-label
      >
        <a-input-password v-model="userInfo.password" :placeholder="`密码`" allow-clear>
          <template #prefix>
            <icon-lock />
          </template>
        </a-input-password>
      </a-form-item>

      <a-form-item
        field="verification_code"
        :rules="[{ required: true, message: ` 验证码不能为空` }]"
        :validate-trigger="['change', 'blur']"
        hide-label
        v-if="false"
      >
        <a-space>
          <a-input v-model="userInfo.verification_code" :placeholder="`验证码`">
            <template #prefix>
              <icon-safe />
            </template>
          </a-input>
          <a-tooltip content="点击刷新验证码">
            <img
              :src="codeUrl"
              @click="reloadCode"
              alt=""
              srcset=""
              class="w-full h-32px rounded-2px cursor-pointer"
            />
          </a-tooltip>
        </a-space>
      </a-form-item>

      <a-space :size="16" direction="vertical">
        <div class="login-form-password-actions">
          <a-checkbox
            checked="rememberPassword"
            :model-value="loginConfig.rememberPassword"
            @change="setRememberPassword as any"
            >记住密码</a-checkbox
          >
          <a-link>忘记密码</a-link>
        </div>
        <a-button type="primary" html-type="submit" long :loading="loading"> 登录 </a-button>
      </a-space>
    </a-form>
  </div>
</template>

<script lang="ts" setup>
import type { ApiError } from '@/api/http'
import type { LoginData } from '@/api/user'
import type { ValidatedError } from '@arco-design/web-vue/es/form/interface'

const config = ref(window.AmisAdmin)

const errorMessage = ref('')
const { loading, setLoading } = useLoading()
const userStore = useUserStore()

const codeUrl = ref(config.value.captchaUrl)

const reloadCode = () => {
  codeUrl.value = `${config.value.captchaUrl}?t=${Date.now()}`
}

const loginConfig = useStorage('login-config', {
  rememberPassword: true,
  username: '',
  password: ''
})
const userInfo = reactive({
  username: loginConfig.value.username,
  password: loginConfig.value.password,
  verification_code: ''
})

const handleSubmit = async ({
  errors,
  values
}: {
  errors: Record<string, ValidatedError> | undefined
  values: Record<string, any>
}) => {
  errorMessage.value = ''
  if (loading.value) return
  if (!errors) {
    setLoading(true)
    try {
      await userStore.login(values as LoginData)

      Message.success('登录成功')

      toRouter({ name: PAGES.home })

      const { rememberPassword } = loginConfig.value
      const { username, password } = values

      loginConfig.value.username = rememberPassword ? username : ''
      loginConfig.value.password = rememberPassword ? password : ''
    } catch (err) {
      errorMessage.value = (err as ApiError).message
    } finally {
      setLoading(false)
    }
  }
}
const setRememberPassword = (value: boolean) => {
  loginConfig.value.rememberPassword = value
}
</script>

<style lang="less" scoped>
.login-form {
  &-wrapper {
    width: 320px;
  }

  &-title {
    color: var(--color-text-1);
    font-weight: 500;
    font-size: 24px;
    line-height: 32px;
  }

  &-sub-title {
    color: var(--color-text-3);
    font-size: 14px;
    line-height: 24px;
  }

  &-error-msg {
    height: 32px;
    color: rgb(var(--red-6));
    line-height: 32px;
  }

  &-password-actions {
    display: flex;
    justify-content: space-between;
  }

  &-register-btn {
    color: var(--color-text-3) !important;
  }
}
</style>
