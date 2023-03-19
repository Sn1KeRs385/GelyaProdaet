import { BaseInput } from 'src/classes/inputs/base-input'
import QuasarToggleComponent from 'src/components/inputs/quasar/QuasarToggle.vue'

interface Constructor {
  label?: string
}

class QuasarToggle extends BaseInput {
  public readonly label?: string
  constructor({ label }: Constructor) {
    super(QuasarToggleComponent)

    this.label = label
  }
  public getParams(): unknown {
    return {
      label: this.label,
    }
  }
}

export default QuasarToggle

export type { Constructor }
