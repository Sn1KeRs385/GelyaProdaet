import ListOptionInterface from 'src/interfaces/models/list-option-interface'
import FileInterface from 'src/interfaces/models/file-interface'
import ProductItemInterface from 'src/interfaces/models/product-item-interface'

export default interface ProductInterface {
  id: number
  description?: string
  price?: number
  price_normalize?: number
  price_final?: number
  price_final_normalize?: number
  is_same_cost_items: boolean
  type: ListOptionInterface
  gender: ListOptionInterface
  brand?: ListOptionInterface
  country?: ListOptionInterface
  items: ProductItemInterface[]
  files?: FileInterface[]
}
