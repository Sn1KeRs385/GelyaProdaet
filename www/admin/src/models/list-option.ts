import BaseModel, { CreateItemInterface } from 'src/models/base-model'
import QTableColParams from 'src/interfaces/quasar/q-table-params'
import FormField from 'src/interfaces/admin/form-field'
import { t } from 'src/boot/i18n'
import QuasarInput from 'src/classes/inputs/quasar/quasar-input'
import QuasarSelect from 'src/classes/inputs/quasar/quasar-select'
import { useListOptionsStore } from 'src/stores/list-options-store'
import OptionGroupSlug from 'src/enums/option-group-slug'

interface AllItemInterface {
  id: number
  group_slug: string
  group_slug_human: string
  title: string
  weight: number
}
interface IndexItemInterface {
  id: number
  group_slug: string
  group_slug_human: string
  title: string
  weight: number
}

class ListOption extends BaseModel<AllItemInterface, IndexItemInterface> {
  protected readonly title = t('models.listOptions.title')
  protected readonly url = 'list-options'

  getTableSettings(): QTableColParams[] {
    return [
      { name: 'id', label: 'Id', field: 'id', sortable: true, align: 'left' },
      {
        name: 'group_slug',
        label: t('models.listOptions.table.group_slug.label'),
        field: 'group_slug',
        format: (val, row) => row.group_slug_human,
        align: 'left',
      },
      {
        name: 'title',
        label: t('models.listOptions.table.title.label'),
        field: 'title',
        align: 'left',
      },
      {
        name: 'weight',
        label: t('models.listOptions.table.weight.label'),
        field: 'weight',
        align: 'left',
      },
    ]
  }

  getFormFields(): FormField[] {
    return [
      {
        key: 'group_slug',
        input: new QuasarSelect({
          label: t('models.listOptions.form.group_slug.label'),
          optionsCallback: () => {
            const listOptionsStore = useListOptionsStore()
            return Object.values(OptionGroupSlug).map((slug) => ({
              value: slug,
              label: listOptionsStore.getHumanSlug(slug),
            }))
          },
        }),
      },
      {
        key: 'title',
        input: new QuasarInput({ label: t('models.listOptions.form.title.label') }),
      },
      {
        key: 'weight',
        input: new QuasarInput({
          label: t('models.listOptions.form.weight.label'),
          type: 'number',
        }),
        defaultValue: 0,
      },
    ]
  }

  create(data: unknown): Promise<CreateItemInterface> {
    return super.create(data).then((response) => {
      useListOptionsStore().loadOptions()
      return response
    })
  }
}

const modelClass = new ListOption()
export default modelClass

export type { AllItemInterface, IndexItemInterface }
