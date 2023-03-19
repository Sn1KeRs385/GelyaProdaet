import { BaseInput } from 'src/classes/inputs/base-input'
import TableComponent from 'src/components/inputs/Table.vue'
import QTableColParams from 'src/interfaces/quasar/q-table-params'
import FormField from 'src/interfaces/admin/form-field'

interface Constructor {
  label: string
  columns: QTableColParams[]
  formFields: FormField[]
}

class Table extends BaseInput {
  public readonly label: string
  public readonly columns: QTableColParams[]
  public readonly formFields: FormField[]
  constructor({ label, columns, formFields }: Constructor) {
    super(TableComponent)

    this.label = label
    this.columns = columns
    this.formFields = formFields
  }
  public getParams(): unknown {
    return {
      label: this.label,
      columns: this.columns,
      formFields: this.formFields,
    }
  }
}

export default Table

export type { Constructor }
