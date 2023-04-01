import { BaseInput } from 'src/classes/inputs/base-input'
import TableComponent from 'src/components/inputs/Table.vue'
import BaseModel from 'src/models/base-model'
import BaseModelInterface from 'src/interfaces/models/base-model-interface'

interface Constructor {
  label: string
  model: BaseModel<BaseModelInterface, BaseModelInterface, BaseModelInterface>
  columnsDelete?: string[]
}

class Table extends BaseInput {
  public readonly label: string
  public readonly model: BaseModel<BaseModelInterface, BaseModelInterface, BaseModelInterface>

  public readonly columnsDelete?: string[]
  constructor({ label, model, columnsDelete }: Constructor) {
    super(TableComponent)

    this.label = label
    this.model = model
    this.columnsDelete = columnsDelete
  }
  public getParams(): unknown {
    return {
      label: this.label,
      model: this.model,
      columnsDelete: this.columnsDelete,
    }
  }
}

export default Table

export type { Constructor }
