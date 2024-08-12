import { createRouter, createWebHistory, type RouteLocationRaw } from 'vue-router'

const prefix = window.AmisAdmin.prefix

export const PAGES = {
  home: 'home',
  login: 'login'
}

const router = createRouter({
  history: createWebHistory(`${prefix}/view/`),
  routes: [
    {
      path: '/',
      name: PAGES.home,
      component: () => import('@/components/layout/BaseLayout.vue'),
      redirect: 'home',
      children: [
        {
          path: '/:pathMatch(.*)*',
          name: 'amis-view',
          component: () => import('@/views/AmisView.vue')
        }
      ]
    },
    {
      path: '/login',
      name: PAGES.login,
      component: () => import('@/views/login/LoginView.vue')
    }
  ]
})

const NO_AUTH_ROUTES = [PAGES.login]

router.beforeEach((to, from, next) => {
  const hasLogin = isLogin() //是否登录
  const isVisitorPage = NO_AUTH_ROUTES.includes(to.name as string) //是否是访客页面

  if (!hasLogin && !isVisitorPage) {
    next({ name: PAGES.login })
  } else if (hasLogin && to.name === PAGES.login) {
    next({ name: PAGES.home })
  } else {
    next()
  }
})

router.afterEach((to) => {
  const hasLogin = isLogin() //是否登录
  const isVisitorPage = NO_AUTH_ROUTES.includes(to.name as string) //是否是访客页面
  if (!hasLogin && !isVisitorPage) {
    toRouter({ name: PAGES.login }, true)
  }

  const menu = useAppStore().getMenuItemByUri(to.path)

  menu && setRouteEmitter(menu)
})

/**
 * 跳转到指定路由
 * @param rt
 * @param replace
 */
export function toRouter(rt: RouteLocationRaw, replace = false) {
  const fn = replace ? router.replace : router.push
  fn(rt)
    .then(() => {})
    .catch((err) => {
      console.log(err)
    })
}

export default router
