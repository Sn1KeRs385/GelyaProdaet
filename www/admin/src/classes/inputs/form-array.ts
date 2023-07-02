import { BaseInput } from 'src/classes/inputs/base-input'
import FormComponent from 'src/components/inputs/FormArray.vue'
import { FormFieldsOrCallback } from 'src/classes/inputs/form'

interface Constructor {
  label?: string
  formFields: FormFieldsOrCallback
  fieldToShowInList?: string
}

class FormArray extends BaseInput {
  public readonly label?: string
  public readonly formFields?: FormFieldsOrCallback
  public readonly fieldToShowInList?: string
  constructor({ label = undefined, formFields, fieldToShowInList = undefined }: Constructor) {
    super(FormComponent)

    this.label = label
    this.formFields = formFields
    this.fieldToShowInList = fieldToShowInList
  }
  public getParams(): unknown {
    return {
      label: this.label,
      formFields: this.formFields,
      fieldToShowInList: this.fieldToShowInList,
    }
  }
}

export default FormArray

export type { Constructor, FormFieldsOrCallback }
