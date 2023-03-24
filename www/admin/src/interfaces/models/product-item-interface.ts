import BaseModelInterface from 'src/interfaces/models/base-model-interface'

export default interface ProductItemInterface extends BaseModelInterface {
  product_id: number
  size_id: number
  color_id: number
  price_buy: number
  price: number
  is_sold: boolean
  is_for_sale: boolean
}
