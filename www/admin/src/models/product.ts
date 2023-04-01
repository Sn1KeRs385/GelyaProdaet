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
import ProductInterface from 'src/interfaces/models/product-interface'
import ListOptionInterface from 'src/interfaces/models/list-option-interface'
import ProductItemInterface from 'src/interfaces/models/product-item-interface'
import ApiFileInterface from 'src/interfaces/Api/file-interface'
import ProductItemWithColorInterface from 'src/interfaces/models/product-item-with-color-interface'
import ProductItemWithSizeInterface from 'src/interfaces/models/product-item-with-size-interface'

interface AllItemInterface {
  id: number
}
interface IndexItemInterface {
  id: number
}
interface GetByIdItemInterface extends ProductInterface {
  files: ApiFileInterface[]
  type: ListOptionInterface
  gender: ListOptionInterface
  brand?: ListOptionInterface
  country?: ListOptionInterface
  items: (ProductItemInterface &
    ProductItemWithColorInterface &
    ProductItemWithSizeInterface & {
      price_buy_normalize: number
      price_normalize: number
    })[]
}

class ProductModel extends BaseModel<AllItemInterface, IndexItemInterface, GetByIdItemInterface> {
  public readonly viewPageComponent = () => import('src/pages/models/product/ViewPage.vue')
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
        key: 'price_buy_normalize',
        input: new QuasarInput({
          label: t('models.product.form.price_buy.label'),
          type: 'number',
        }),
      },
      {
        key: 'price_normalize',
        input: new QuasarInput({
          label: t('models.product.form.price.label'),
          type: 'number',
        }),
      },
      {
        key: 'send_to_telegram',
        input: new QuasarToggle({
          label: t('models.product.form.sendToTelegram.label'),
        }),
        defaultValue: true,
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
              format: (val) => useListOptionsStore().getOptionById(val)?.title || '-',
              align: 'left',
            },
            {
              name: 'color_id',
              label: t('models.productItem.table.color.label'),
              field: 'color_id',
              format: (val) => useListOptionsStore().getOptionById(val)?.title || '-',
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
              key: 'count',
              input: new QuasarInput({
                label: t('models.productItem.form.count.label'),
                type: 'number',
              }),
              defaultValue: 1,
            },
            {
              key: 'price_buy_normalize',
              input: new QuasarInput({
                label: t('models.product.form.price_buy.label'),
                type: 'number',
              }),
            },
            {
              key: 'price_normalize',
              input: new QuasarInput({
                label: t('models.product.form.price.label'),
                type: 'number',
              }),
            },
            {
              key: 'is_for_sale',
              input: new QuasarToggle({
                label: t('models.productItem.form.is_for_sale.label'),
              }),
              defaultValue: true,
              hideInUpdate: true,
            },
          ],
        }),
      },
    ]
  }
}

const modelClass = new ProductModel()
export default modelClass

export type { AllItemInterface, IndexItemInterface, GetByIdItemInterface }
