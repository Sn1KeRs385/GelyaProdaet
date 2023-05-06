import BaseModel, { CreateItemInterface, DefaultSortInterface } from 'src/models/base-model'
import QTableColParams from 'src/interfaces/quasar/q-table-params'
import FormField from 'src/interfaces/admin/form-field'
import { t } from 'src/boot/i18n'
import QuasarInput from 'src/classes/inputs/quasar/quasar-input'
import QuasarSelect from 'src/classes/inputs/quasar/quasar-select'
import { useListOptionsStore } from 'src/stores/list-options-store'
import OptionGroupSlug from 'src/enums/option-group-slug'
import ListOptionInterface from 'src/interfaces/models/list-option-interface'
import FileUploader from 'src/classes/inputs/file-uploader'
import CollectionName from 'file-uploader/enums/collection-name'

type AllItemInterface = ListOptionInterface
type IndexItemInterface = ListOptionInterface
type GetByIdItemInterface = ListOptionInterface

class ListOption extends BaseModel<AllItemInterface, IndexItemInterface, GetByIdItemInterface> {
  protected readonly title = t('models.listOptions.title')
  protected readonly url = 'list-options'

  getTableDefaultSort(): DefaultSortInterface {
    const params = super.getTableDefaultSort()

    params.sortBy = undefined
    params.desc = undefined

    return params
  }
  getTableSettings(): QTableColParams[] {
    return [
      {
        name: 'id',
        label: 'Id',
        field: 'id',
        sortable: false,
        align: 'left',
      },
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
      {
        key: 'logo_ids',
        input: new FileUploader({
          label: t('models.listOptions.form.logo.label'),
          collectionName: CollectionName.LIST_OPTION_LOGO,
          filesField: 'logo',
          fileUploaderOptions: {
            multiple: false,
            accept: 'image/*',
          },
        }),
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

export type { AllItemInterface, IndexItemInterface, GetByIdItemInterface }
