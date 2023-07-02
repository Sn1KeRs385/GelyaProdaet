import { BaseInput } from 'src/classes/inputs/base-input'

// eslint-disable-next-line @typescript-eslint/no-explicit-any
type BaseInputOrCallback = BaseInput | ((model: any) => BaseInput)

export default interface FormField {
  key: string
  input: BaseInputOrCallback
  defaultValue?: string | number | boolean | string[] | number[] | boolean[]
  hideInCreate?: boolean
  hideInUpdate?: boolean
  tabName?: string
}

export type { BaseInputOrCallback }
