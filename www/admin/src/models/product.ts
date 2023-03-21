import BaseModel from 'src/models/base-model'
import QTableColParams from 'src/interfaces/quasar/q-table-params'
import FormField from 'src/interfaces/admin/form-field'
import { t } from 'src/boot/i18n'
import QuasarInput from 'src/classes/inputs/quasar/quasar-input'
import QuasarSelect from 'src/classes/inputs/quasar/quasar-select'
import { useListOptionsStore } from 'src/stores/list-options-store'
import OptionGroupSlug from 'src/enums/option-group-slug'
import Table from 'src/classes/inputs/table'
import QuasarToggle from 'src/classes/inputs/quasar/quasar-toggle'
import FileUploader from 'src/classes/inputs/file-uploader'

interface AllItemInterface {
  id: number
}
interface IndexItemInterface {
  id: number
}

class ProductModel extends BaseModel<AllItemInterface, IndexItemInterface> {
  protected readonly title = t('models.product.title')
  protected readonly url = 'products'

  getTableSettings(): QTableColParams[] {
    return [
      { name: 'id', label: 'Id', field: 'id', sortable: true, align: 'left' },
      {
        name: 'title',
        label: t('models.product.table.title.label'),
        field: 'title',
        align: 'left',
      },
      {
        name: 'type',
        label: t('models.product.table.type.label'),
        field: 'type',
        format: (val) => val?.title || '-',
        align: 'left',
      },
      {
        name: 'gender',
        label: t('models.product.table.gender.label'),
        field: 'gender',
        format: (val) => val?.title || '-',
        align: 'left',
      },
      {
        name: 'brand',
        label: t('models.product.table.brand.label'),
        field: 'brand',
        format: (val) => val?.title || '-',
        align: 'left',
      },
      {
        name: 'country',
        label: t('models.product.table.country.label'),
        field: 'country',
        format: (val) => val?.title || '-',
        align: 'left',
      },
    ]
  }

  getFormFields(): FormField[] {
    return [
      {
        key: 'files_ids',
        input: new FileUploader({
          label: t('models.product.form.photos.label'),
          fileUploaderOptions: {
            multiple: true,
            accept: 'image/*',
          },
        }),
      },
      {
        key: 'title',
        input: new QuasarInput({ label: t('models.product.form.title.label') }),
      },
      {
        key: 'description',
        input: new QuasarInput({ label: t('models.product.form.description.label') }),
      },
      {
        key: 'type_id',
        input: new QuasarSelect({
          label: useListOptionsStore().getHumanSlug(OptionGroupSlug.PRODUCT_TYPE),
          optionsCallback: () =>
            useListOptionsStore().getSelectMappedOptionBySlug(OptionGroupSlug.PRODUCT_TYPE),
        }),
      },
      {
        key: 'gender_id',
        input: new QuasarSelect({
          label: useListOptionsStore().getHumanSlug(OptionGroupSlug.GENDER),
          optionsCallback: () =>
            useListOptionsStore().getSelectMappedOptionBySlug(OptionGroupSlug.GENDER),
        }),
      },
      {
        key: 'brand_id',
        input: new QuasarSelect({
          label: useListOptionsStore().getHumanSlug(OptionGroupSlug.BRAND),
          optionsCallback: () =>
            useListOptionsStore().getSelectMappedOptionBySlug(OptionGroupSlug.BRAND),
        }),
      },
      {
        key: 'country_id',
        input: new QuasarSelect({
          label: useListOptionsStore().getHumanSlug(OptionGroupSlug.COUNTRY),
          optionsCallback: () =>
            useListOptionsStore().getSelectMappedOptionBySlug(OptionGroupSlug.COUNTRY),
        }),
      },
      {
        key: 'price_buy',
        input: new QuasarInput({
          label: t('models.product.form.price_buy.label'),
          type: 'number',
        }),
      },
      {
        key: 'price',
        input: new QuasarInput({
          label: t('models.product.form.price.label'),
          type: 'number',
        }),
      },
      {
        key: 'items',
        input: new Table({
          label: t('models.product.form.items.label'),
          columns: [
            {
              name: 'id',
              label: 'Id',
              field: 'id',
              align: 'left',
            },
            {
              name: 'size_id',
              label: t('models.productItem.table.size.label'),
              field: 'size_id',
              format: (val) =>
                useListOptionsStore().getOptionById(val)?.title || t('texts.unknown'),
              align: 'left',
            },
            {
              name: 'color_id',
              label: t('models.productItem.table.color.label'),
              field: 'color_id',
              format: (val) =>
                useListOptionsStore().getOptionById(val)?.title || t('texts.unknown'),
              align: 'left',
            },
            {
              name: 'price_buy',
              label: t('models.productItem.table.price_buy.label'),
              field: 'price_buy',
              align: 'left',
            },
            {
              name: 'price',
              label: t('models.productItem.table.price.label'),
              field: 'price',
              align: 'left',
            },
            {
              name: 'is_sold',
              label: t('models.productItem.table.is_sold.label'),
              field: 'is_sold',
              format: (val) => (val ? t('texts.yes') : t('texts.no')),
              align: 'left',
            },
            {
              name: 'is_for_sale',
              label: t('models.productItem.table.is_for_sale.label'),
              field: 'is_for_sale',
              format: (val) => (val ? t('texts.yes') : t('texts.no')),
              align: 'left',
            },
          ],
          formFields: [
            {
              key: 'size_id',
              input: new QuasarSelect({
                label: useListOptionsStore().getHumanSlug(OptionGroupSlug.SIZE),
                optionsCallback: () =>
                  useListOptionsStore().getSelectMappedOptionBySlug(OptionGroupSlug.SIZE),
              }),
            },
            {
              key: 'color_id',
              input: new QuasarSelect({
                label: useListOptionsStore().getHumanSlug(OptionGroupSlug.COLOR),
                optionsCallback: () =>
                  useListOptionsStore().getSelectMappedOptionBySlug(OptionGroupSlug.COLOR),
              }),
            },
            {
              key: 'price_buy',
              input: new QuasarInput({
                label: t('models.productItem.form.price_buy.label'),
                type: 'number',
              }),
            },
            {
              key: 'price',
              input: new QuasarInput({
                label: t('models.productItem.form.price.label'),
                type: 'number',
              }),
            },
            {
              key: 'is_for_sale',
              input: new QuasarToggle({
                label: t('models.productItem.form.is_for_sale.label'),
              }),
              defaultValue: true,
            },
          ],
        }),
      },
    ]
  }
}

const modelClass = new ProductModel()
export default modelClass

export type { AllItemInterface, IndexItemInterface }
