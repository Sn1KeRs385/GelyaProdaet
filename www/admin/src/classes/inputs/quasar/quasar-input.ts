import { BaseInput } from 'src/classes/inputs/base-input'
import QuasarInputComponent from 'src/components/inputs/quasar/QuasarInput.vue'

interface Constructor {
  type?:
    | 'text'
    | 'password'
    | 'textarea'
    | 'email'
    | 'search'
    | 'tel'
    | 'file'
    | 'number'
    | 'url'
    | 'time'
    | 'date'
  label?: string
  placeholder?: string
}

class QuasarInput extends BaseInput {
  public readonly type:
    | 'text'
    | 'password'
    | 'textarea'
    | 'email'
    | 'search'
    | 'tel'
    | 'file'
    | 'number'
    | 'url'
    | 'time'
    | 'date'
  public readonly label?: string
  public readonly placeholder?: string
  constructor({ placeholder, label, type }: Constructor) {
    super(QuasarInputComponent)

    this.type = type || 'text'
    this.label = label
    this.placeholder = placeholder
  }
  public getParams(): unknown {
    return {
      type: this.type,
      placeholder: this.placeholder,
      label: this.label,
    }
  }
}

export default QuasarInput

export type { Constructor }
