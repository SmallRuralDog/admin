import type { AmisAdmin } from '@/stores/modules/app/types'

export {}
declare global {
  interface Window {
    AmisAdmin: AmisAdmin
    amisRequire: any
  }
}
