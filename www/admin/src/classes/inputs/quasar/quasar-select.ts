import { BaseInput } from 'src/classes/inputs/base-input'
import QuasarSelectComponent from 'src/components/inputs/quasar/QuasarSelect.vue'
import { QSelectOption } from 'quasar'

type OptionsCallback = () => QSelectOption<string | number>[]
interface Constructor {
  label?: string
  optionsCallback: OptionsCallback
}

class QuasarInput extends BaseInput {
  public readonly label?: string
  public readonly optionsCallback: OptionsCallback
  constructor({ label, optionsCallback }: Constructor) {
    super(QuasarSelectComponent)

    this.label = label
    this.optionsCallback = optionsCallback
  }
  public getParams(): unknown {
    return {
      label: this.label,
      optionsCallback: this.optionsCallback,
      emitValue: true,
      mapOptions: true,
    }
  }
}

export default QuasarInput

export type { Constructor, OptionsCallback }
