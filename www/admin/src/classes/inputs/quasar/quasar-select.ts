import { BaseInput } from 'src/classes/inputs/base-input'
import QuasarSelectComponent from 'src/components/inputs/quasar/QuasarSelect.vue'
import { QSelectOption } from 'quasar'

type OptionsCallback = () => QSelectOption<string | number>[]
type CreateCallback = (label: string) => Promise<string | number>
interface Constructor {
  label?: string
  tooltip?: string
  multiple?: boolean
  optionsCallback: OptionsCallback
  createCallback?: CreateCallback
  required?: boolean
}

class QuasarSelect extends BaseInput {
  public readonly label?: string
  public readonly tooltip?: string
  public readonly multiple: boolean
  public readonly optionsCallback: OptionsCallback
  public readonly createCallback?: CreateCallback
  public readonly required: boolean
  constructor({
    label,
    tooltip,
    optionsCallback,
    createCallback,
    multiple,
    required,
  }: Constructor) {
    super(QuasarSelectComponent)

    this.label = label
    this.tooltip = tooltip
    this.multiple = multiple || false
    this.optionsCallback = optionsCallback
    this.createCallback = createCallback
    this.required = required || false
  }
  public getParams(): unknown {
    return {
      label: this.label,
      tooltip: this.tooltip,
      multiple: this.multiple,
      optionsCallback: this.optionsCallback,
      createCallback: this.createCallback,
      emitValue: true,
      mapOptions: true,
      clearable: true,
      required: this.required,
    }
  }
}

export default QuasarSelect

export type { Constructor, OptionsCallback, CreateCallback }
