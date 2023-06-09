import BaseModel from 'src/models/base-model'
import QTableColParams from 'src/interfaces/quasar/q-table-params'
import FormField from 'src/interfaces/admin/form-field'
import { t } from 'src/boot/i18n'
import ListOptionInterface from 'src/interfaces/models/list-option-interface'
import ProductItemInterface from 'src/interfaces/models/product-item-interface'
import { api } from 'src/boot/axios'
import QuasarSelect from 'src/classes/inputs/quasar/quasar-select'
import { useListOptionsStore } from 'src/stores/list-options-store'
import OptionGroupSlug from 'src/enums/option-group-slug'
import QuasarToggle from 'src/classes/inputs/quasar/quasar-toggle'
import BaseModelInterface from 'src/interfaces/models/base-model-interface'
import QuasarInput from 'src/classes/inputs/quasar/quasar-input'
import ProductItemWithNormalizePricesInterface from 'src/interfaces/models/product-item-with-normalize-prices-interface'
import ListOption from 'src/models/list-option'

interface AllItemInterface extends BaseModelInterface {
  id: number
}
interface IndexItemInterface extends BaseModelInterface {
  id: number
}
interface GetByIdItemInterface extends ProductItemInterface {
  size: ListOptionInterface
  color?: ListOptionInterface
}

type AfterStatusManipulateInterface = ProductItemInterface & ProductItemWithNormalizePricesInterface

class ProductItemModel extends BaseModel<
  AllItemInterface,
  IndexItemInterface,
  GetByIdItemInterface
> {
  public readonly viewPageComponent = () => import('src/pages/models/product/ViewPage.vue')
  protected readonly title = t('models.productItem.title')
  protected readonly url = 'product-items'

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
        name: 'product',
        label: t('models.productItem.table.product.label'),
        field: 'product',
        format: (val) => val?.title || '-',
      },
      {
        name: 'size_id',
        label: t('models.productItem.table.size.label'),
        field: 'size_id',
        format: (val) => useListOptionsStore().getOptionById(val)?.title || '-',
      },
      {
        name: 'size_year_id',
        label: t('models.productItem.table.size_year.label'),
        field: 'size_year_id',
        format: (val) => useListOptionsStore().getOptionById(val)?.title || '-',
      },
      {
        name: 'color_id',
        label: t('models.productItem.table.color.label'),
        field: 'color_id',
        format: (val) => useListOptionsStore().getOptionById(val)?.title || '-',
      },
      {
        name: 'price_buy_normalize',
        label: t('models.productItem.table.price_buy.label'),
        field: 'price_buy_normalize',
      },
      {
        name: 'price_normalize',
        label: t('models.productItem.table.price.label'),
        field: 'price_normalize',
      },
      {
        name: 'price_final_normalize',
        label: t('models.productItem.table.price_final.label'),
        field: 'price_final_normalize',
      },
      {
        name: 'count',
        label: t('models.productItem.table.count.label'),
        field: 'count',
      },
      {
        name: 'is_sold',
        label: t('models.productItem.table.is_sold.label'),
        field: 'is_sold',
        format: (val) => (val ? t('texts.yes') : t('texts.no')),
      },
      {
        name: 'is_for_sale',
        label: t('models.productItem.table.is_for_sale.label'),
        field: 'is_for_sale',
        format: (val) => (val ? t('texts.yes') : t('texts.no')),
      },
      {
        name: 'is_reserved',
        label: t('models.productItem.table.is_reserved.label'),
        field: 'is_reserved',
        format: (val) => (val ? t('texts.yes') : t('texts.no')),
      },
    ]
  }

  getFormFields(): FormField[] {
    return [
      {
        key: 'size_id',
        input: new QuasarSelect({
          label: useListOptionsStore().getHumanSlug(OptionGroupSlug.SIZE),
          optionsCallback: () =>
            useListOptionsStore().getSelectMappedOptionBySlug(OptionGroupSlug.SIZE),
          createCallback: (value: string) =>
            ListOption.create({
              group_slug: OptionGroupSlug.SIZE,
              title: value,
            }).then((response) => response.id),
        }),
      },
      {
        key: 'size_year_id',
        input: new QuasarSelect({
          label: useListOptionsStore().getHumanSlug(OptionGroupSlug.SIZE_YEAR),
          optionsCallback: () =>
            useListOptionsStore().getSelectMappedOptionBySlug(OptionGroupSlug.SIZE_YEAR),
          createCallback: (value: string) =>
            ListOption.create({
              group_slug: OptionGroupSlug.SIZE_YEAR,
              title: value,
            }).then((response) => response.id),
        }),
      },
      {
        key: 'color_id',
        input: new QuasarSelect({
          label: useListOptionsStore().getHumanSlug(OptionGroupSlug.COLOR),
          optionsCallback: () =>
            useListOptionsStore().getSelectMappedOptionBySlug(OptionGroupSlug.COLOR),
          createCallback: (value: string) =>
            ListOption.create({
              group_slug: OptionGroupSlug.COLOR,
              title: value,
            }).then((response) => response.id),
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
          label: t('models.productItem.form.price_buy.label'),
          type: 'number',
        }),
      },
      {
        key: 'price_normalize',
        input: new QuasarInput({
          label: t('models.productItem.form.price.label'),
          type: 'number',
        }),
      },
      {
        key: 'price_final_normalize',
        input: new QuasarInput({
          label: t('models.productItem.form.price_final.label'),
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
    ]
  }

  markSold(
    id: number,
    priceSell: number | undefined = undefined
  ): Promise<AfterStatusManipulateInterface> {
    return api
      .request({
        method: 'post',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/${id}/mark-sold`,
        params: {
          price_sell: priceSell,
        },
      })
      .then((response) => response.data)
  }
  changePriceSell(id: number, priceSell: number): Promise<AfterStatusManipulateInterface> {
    return api
      .request({
        method: 'post',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/${id}/change-price-sell`,
        params: {
          price_sell: priceSell,
        },
      })
      .then((response) => response.data)
  }
  markNotForSale(id: number): Promise<AfterStatusManipulateInterface> {
    return api
      .request({
        method: 'post',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/${id}/mark-not-for-sale`,
      })
      .then((response) => response.data)
  }
  rollbackForSaleStatus(id: number): Promise<AfterStatusManipulateInterface> {
    return api
      .request({
        method: 'post',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/${id}/rollback-for-sale-status`,
      })
      .then((response) => response.data)
  }
  switchReserve(id: number): Promise<AfterStatusManipulateInterface> {
    return api
      .request({
        method: 'post',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/${id}/switch-reserve`,
      })
      .then((response) => response.data)
  }
}

const modelClass = new ProductItemModel()
export default modelClass

export type {
  AllItemInterface,
  IndexItemInterface,
  GetByIdItemInterface,
  AfterStatusManipulateInterface,
}
