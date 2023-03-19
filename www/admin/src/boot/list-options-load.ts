import { boot } from 'quasar/wrappers'
import { useListOptionsStore } from 'src/stores/list-options-store'
export default boot(({ store }) => {
  const listOptionsStore = useListOptionsStore(store)
  listOptionsStore.loadOptions()
})
