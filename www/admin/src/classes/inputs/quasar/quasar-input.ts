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
  tooltip?: string
  required?: boolean
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
  public readonly tooltip?: string
  public readonly required: boolean
  constructor({ placeholder, label, type, tooltip, required }: Constructor) {
    super(QuasarInputComponent)

    this.type = type || 'text'
    this.label = label
    this.placeholder = placeholder
    this.tooltip = tooltip
    this.required = required || false
  }
  public getParams(): unknown {
    return {
      type: this.type,
      placeholder: this.placeholder,
      label: this.label,
      tooltip: this.tooltip,
      required: this.required,
    }
  }
}

export default QuasarInput

export type { Constructor }
