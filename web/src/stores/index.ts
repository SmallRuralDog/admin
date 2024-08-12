import { createPinia } from 'pinia'

import useTabBarStore from './modules/tab-bar'
import useAppStore from './modules/app'
import useUserStore from './modules/user'
import usePagesStore from './modules/page'

const pinia = createPinia()

export { useAppStore, useUserStore, useTabBarStore, usePagesStore }
export default pinia
