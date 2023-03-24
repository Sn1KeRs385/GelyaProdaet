import BaseModelInterface from 'src/interfaces/models/base-model-interface'

export default interface ProductInterface extends BaseModelInterface {
  title: string
  description?: string
  type_id: number
  gender_id: number
  brand_id: number
  country_id: number
}
