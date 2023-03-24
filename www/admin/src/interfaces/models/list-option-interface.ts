import BaseModelInterface from 'src/interfaces/models/base-model-interface'

export default interface ListOptionInterface extends BaseModelInterface {
  group_slug: string
  group_slug_human: string
  title: string
  weight: number
}
