import { EventBus } from 'quasar'
import { boot } from 'quasar/wrappers'

const eventBus = new EventBus<{
  openOzonDataPopup: (productId: number) => void
}>()

export default boot(({ app }) => {
  // for Options API
  app.config.globalProperties.$bus = eventBus

  // for Composition API
  app.provide('bus', eventBus)
})

export { eventBus }
