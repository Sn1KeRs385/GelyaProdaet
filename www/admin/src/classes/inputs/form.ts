import { BaseInput } from 'src/classes/inputs/base-input'
import FormComponent from 'src/components/inputs/Form.vue'
import FormField from 'src/interfaces/admin/form-field'

// eslint-disable-next-line @typescript-eslint/no-explicit-any
type FormFieldsOrCallback = FormField[] | ((model: any) => FormField[])

interface Constructor {
  label?: string
  formFields: FormFieldsOrCallback
}

class Form extends BaseInput {
  public readonly label?: string
  public readonly formFields?: FormFieldsOrCallback
  constructor({ label = undefined, formFields }: Constructor) {
    super(FormComponent)

    this.label = label
    this.formFields = formFields
  }
  public getParams(): unknown {
    return {
      label: this.label,
      formFields: this.formFields,
    }
  }
}

export default Form

export type { Constructor, FormFieldsOrCallback }
