import { BaseInput } from 'src/classes/inputs/base-input'
import QuasarTreeComponent from 'src/components/inputs/quasar/QuasarTree.vue'
import { QTreeNode, QTreeProps } from 'quasar'

type OptionsCallback = () => QTreeNode[]
type OptionSelectedHook = (node: QTreeNode | undefined) => void
interface Constructor {
  label?: string
  optionsCallback: OptionsCallback
  optionSelectedHook?: OptionSelectedHook
  params?: Partial<QTreeProps>
}

class QuasarTree extends BaseInput {
  public readonly label?: string
  public readonly optionsCallback: OptionsCallback
  public readonly optionSelectedHook?: OptionSelectedHook
  public readonly params?: Partial<QTreeProps>
  constructor({ label, optionsCallback, optionSelectedHook, params }: Constructor) {
    super(QuasarTreeComponent)

    this.label = label
    this.optionsCallback = optionsCallback
    this.optionSelectedHook = optionSelectedHook
    this.params = params
  }
  public getParams(): unknown {
    return {
      label: this.label,
      optionsCallback: this.optionsCallback,
      optionSelectedHook: this.optionSelectedHook,
      params: this.params,
    }
  }
}

export default QuasarTree

export type { Constructor, OptionsCallback, OptionSelectedHook }
