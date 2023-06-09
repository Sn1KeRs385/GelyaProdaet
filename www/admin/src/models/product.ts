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
import ProductItemModel from 'src/models/product-item'
import ProductItemWithNormalizePricesInterface from 'src/interfaces/models/product-item-with-normalize-prices-interface'
import ProductItemWithSizeYearInterface from 'src/interfaces/models/product-item-with-size-year-interface'
import ListOption from 'src/models/list-option'
import { api } from 'src/boot/axios'

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
    ProductItemWithSizeInterface &
    ProductItemWithNormalizePricesInterface)[]
}

class ProductModel extends BaseModel<AllItemInterface, IndexItemInterface, GetByIdItemInterface> {
  public readonly viewPageComponent = () => import('src/pages/models/product/ViewPage.vue')
  protected readonly title = t('models.product.title')
  protected readonly url = 'products'

  getTableSettings(): QTableColParams[] {
    return [
      {
        name: 'id',
        label: 'Id',
        field: 'id',
        sortable: true,
        align: 'left',
      },
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
      {
        name: 'telegram_info',
        label: t('models.product.table.telegram_info.label'),
        field: 'telegram_info',
        format: (val, row) =>
          (row.send_to_telegram ? t('texts.yes') : t('texts.no')) +
          '/' +
          (row.is_send_to_telegram ? t('texts.yes') : t('texts.no')),
        align: 'left',
      },
      {
        name: 'sizes',
        label: t('models.product.table.sizes.label'),
        field: 'sizes',
        format: (val, row) => {
          const titles = row.items
            .map((item: ProductItemWithSizeInterface) => item.size?.title)
            .filter((title: string) => !!title)

          if (titles.length === 0) {
            return '-'
          }

          return titles.join(', ')
        },
        align: 'left',
      },
      {
        name: 'size_years',
        label: t('models.product.table.size_years.label'),
        field: 'size_years',
        format: (val, row) => {
          const titles = row.items
            .map((item: ProductItemWithSizeYearInterface) => item.size_year?.title)
            .filter((title: string) => !!title)

          if (titles.length === 0) {
            return '-'
          }

          return titles.join(', ')
        },
        align: 'left',
      },
      {
        name: 'is_sold',
        label: t('models.product.table.is_sold.label'),
        field: 'is_sold',
        format: (val, row) =>
          row.items.filter((item: ProductItemInterface) => item.is_sold).length +
          '/' +
          row.items.length,
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
          createCallback: (value: string) =>
            ListOption.create({
              group_slug: OptionGroupSlug.PRODUCT_TYPE,
              title: value,
            }).then((response) => response.id),
        }),
      },
      {
        key: 'gender_id',
        input: new QuasarSelect({
          label: useListOptionsStore().getHumanSlug(OptionGroupSlug.GENDER),
          optionsCallback: () =>
            useListOptionsStore().getSelectMappedOptionBySlug(OptionGroupSlug.GENDER),
          createCallback: (value: string) =>
            ListOption.create({
              group_slug: OptionGroupSlug.GENDER,
              title: value,
            }).then((response) => response.id),
        }),
      },
      {
        key: 'brand_id',
        input: new QuasarSelect({
          label: useListOptionsStore().getHumanSlug(OptionGroupSlug.BRAND),
          optionsCallback: () =>
            useListOptionsStore().getSelectMappedOptionBySlug(OptionGroupSlug.BRAND),
          createCallback: (value: string) =>
            ListOption.create({
              group_slug: OptionGroupSlug.BRAND,
              title: value,
            }).then((response) => response.id),
        }),
      },
      {
        key: 'country_id',
        input: new QuasarSelect({
          label: useListOptionsStore().getHumanSlug(OptionGroupSlug.COUNTRY),
          optionsCallback: () =>
            useListOptionsStore().getSelectMappedOptionBySlug(OptionGroupSlug.COUNTRY),
          createCallback: (value: string) =>
            ListOption.create({
              group_slug: OptionGroupSlug.COUNTRY,
              title: value,
            }).then((response) => response.id),
        }),
      },
      {
        key: 'price_buy_normalize',
        input: new QuasarInput({
          label: t('models.product.form.price_buy.label'),
          type: 'number',
        }),
        hideInUpdate: true,
      },
      {
        key: 'price_normalize',
        input: new QuasarInput({
          label: t('models.product.form.price.label'),
          type: 'number',
        }),
        hideInUpdate: true,
      },
      {
        key: 'price_final_normalize',
        input: new QuasarInput({
          label: t('models.product.form.price_final.label'),
          type: 'number',
        }),
        hideInCreate: true,
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
          model: ProductItemModel,
          columnsDelete: ['product'],
        }),
      },
    ]
  }

  resendToTelegram(id: number): Promise<ProductInterface> {
    return api
      .request({
        method: 'post',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/${id}/resend-to-telegram`,
      })
      .then((response) => response.data)
  }

  sendToMyTelegram(id: number): Promise<ProductInterface> {
    return api
      .request({
        method: 'post',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/${id}/send-to-my-telegram`,
      })
      .then((response) => response.data)
  }
}

const modelClass = new ProductModel()
export default modelClass

export type { AllItemInterface, IndexItemInterface, GetByIdItemInterface }
