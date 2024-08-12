import type { MenuItem } from '@/stores/modules/user/types'
import mitt, { type Handler } from 'mitt'

const emitter = mitt()

const key = Symbol('ROUTE_CHANGE')

let latestRoute: MenuItem

export function setRouteEmitter(to: MenuItem) {
  emitter.emit(key, to)
  latestRoute = to
}

export function listenerRouteChange(handler: (route: MenuItem) => void, immediate = true) {
  emitter.on(key, handler as Handler)
  if (immediate && latestRoute) {
    handler(latestRoute)
  }
}

export function removeRouteListener() {
  emitter.off(key)
}
