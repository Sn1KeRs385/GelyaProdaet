import { defineStore } from 'pinia'
import ProductInterface from 'src/interfaces/models/product-interface'

interface ProductsStore {
  products: ProductInterface[]
}

export const useProductsStore = defineStore('products', {
  state: (): ProductsStore => ({
    products: [],
  }),
  getters: {},
  actions: {
    setProducts(products: ProductInterface[]) {
      this.products = products
    },
  },
})
