import type { LoginData } from '@/api/user'
import type { UserState } from './types'

const useUserStore = defineStore('user', {
  state: (): UserState => ({
    name: undefined,
    avatar: undefined,
    job: undefined,
    organization: undefined,
    location: undefined,
    email: undefined,
    introduction: undefined,
    personalWebsite: undefined,
    jobName: undefined,
    organizationName: undefined,
    locationName: undefined,
    phone: undefined,
    registrationDate: undefined,
    accountId: undefined,
    certification: undefined,
    role: ''
  }),

  getters: {
    userInfo(state: UserState): UserState {
      return { ...state }
    }
  },

  actions: {
    switchRoles() {
      return new Promise((resolve) => {
        this.role = this.role === 'user' ? 'admin' : 'user'
        resolve(this.role)
      })
    },
    // Set user's information
    setInfo(partial: Partial<UserState>) {
      this.$patch(partial)
    },

    // Reset user's information
    resetInfo() {
      this.$reset()
    },
    async login(loginForm: LoginData) {
      try {
        const res = await userLogin(loginForm)
        setToken(res.data.access_token)
      } catch (err) {
        clearToken()
        throw err
      }
    },
    logoutCallBack() {
      const appStore = useAppStore()
      this.resetInfo()
      clearToken()
      removeRouteListener()
      appStore.clearServerMenu()
      toRouter({ name: PAGES.login }, true)
    },
    async logout() {
      try {
        await userLogout()
      } finally {
        this.logoutCallBack()
      }
    }
  }
})

export default useUserStore
