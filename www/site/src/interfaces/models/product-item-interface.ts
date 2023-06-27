import ListOptionInterface from 'src/interfaces/models/list-option-interface'

export default interface ProductItemInterface {
  id: number
  price: number
  price_normalize: number
  price_final?: number
  price_final_normalize?: number
  is_sold: boolean
  count: number
  is_reserved: boolean
  size?: ListOptionInterface
  size_year?: ListOptionInterface
  color?: ListOptionInterface
}
