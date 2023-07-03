import BaseModelInterface from 'src/interfaces/models/base-model-interface'

export default interface OzonDataInterface extends BaseModelInterface {
  product_id: string
  category_id?: string
  dept: number
  height: number
  width: number
  weight: number
  attributes: unknown[]
}
