import { defineStore } from 'pinia'
import { eventBus } from 'src/boot/event-bus'
import { QTreeNode } from 'quasar'
import OzonDataModel from 'src/models/ozon-data'

interface OzonCategory {
  category_id: number
  title: string
  children: OzonCategory[]
}

interface OzonDataStore {
  isShowPopup: boolean
  categories: OzonCategory[]
}

export const useOzonDataStore = defineStore('ozon-data', {
  state: (): OzonDataStore => ({
    isShowPopup: false,
    categories: [],
  }),
  getters: {},
  actions: {
    openPopup(productId: number): void {
      eventBus.emit('openOzonDataPopup', productId)
    },
    loadCategories() {
      OzonDataModel.getCategories().then((result) => {
        this.categories = result
      })
    },
    getCategoriesTree(): QTreeNode[] {
      const categoryHandler = (category: OzonCategory): QTreeNode => {
        return {
          label: category.title,
          category_id: category.category_id,
          children: category.children.map(categoryHandler),
          selectable: category.children.length === 0,
        }
      }

      return this.categories.map(categoryHandler)
    },
  },
})

export type { OzonCategory }
