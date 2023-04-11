import { BaseInput } from 'src/classes/inputs/base-input'
import QuasarSelectComponent from 'src/components/inputs/quasar/QuasarSelect.vue'
import { QSelectOption } from 'quasar'

type OptionsCallback = () => QSelectOption<string | number>[]
type CreateCallback = (label: string) => Promise<string | number>
interface Constructor {
  label?: string
  optionsCallback: OptionsCallback
  createCallback?: CreateCallback
}

class QuasarInput extends BaseInput {
  public readonly label?: string
  public readonly optionsCallback: OptionsCallback
  public readonly createCallback?: CreateCallback
  constructor({ label, optionsCallback, createCallback }: Constructor) {
    super(QuasarSelectComponent)

    this.label = label
    this.optionsCallback = optionsCallback
    this.createCallback = createCallback
  }
  public getParams(): unknown {
    return {
      label: this.label,
      optionsCallback: this.optionsCallback,
      createCallback: this.createCallback,
      emitValue: true,
      mapOptions: true,
      clearable: true,
    }
  }
}

export default QuasarInput

export type { Constructor, OptionsCallback, CreateCallback }
