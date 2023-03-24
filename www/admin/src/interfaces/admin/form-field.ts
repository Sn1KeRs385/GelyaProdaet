import { BaseInput } from 'src/classes/inputs/base-input'

export default interface FormField {
  key: string
  input: BaseInput
  defaultValue?: string | number | boolean | string[] | number[] | boolean[]
  hideInCreate?: boolean
  hideInUpdate?: boolean
}
