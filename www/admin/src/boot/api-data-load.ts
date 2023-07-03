import { boot } from 'quasar/wrappers'
import { useListOptionsStore } from 'src/stores/list-options-store'
import { useOzonDataStore } from 'src/stores/ozon-data-store'
export default boot(({ store }) => {
  useListOptionsStore(store).loadOptions()
  useOzonDataStore(store).loadCategories()
})
