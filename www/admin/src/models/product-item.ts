import BaseModel from 'src/models/base-model'
import QTableColParams from 'src/interfaces/quasar/q-table-params'
import FormField from 'src/interfaces/admin/form-field'
import { t } from 'src/boot/i18n'
import ListOptionInterface from 'src/interfaces/models/list-option-interface'
import ProductItemInterface from 'src/interfaces/models/product-item-interface'
import { api } from 'src/boot/axios'

interface AllItemInterface {
  id: number
}
interface IndexItemInterface {
  id: number
}
interface GetByIdItemInterface extends ProductItemInterface {
  size: ListOptionInterface
  color?: ListOptionInterface
}

class ProductItemModel extends BaseModel<
  AllItemInterface,
  IndexItemInterface,
  GetByIdItemInterface
> {
  public readonly viewPageComponent = () => import('src/pages/models/product/ViewPage.vue')
  protected readonly title = t('models.productItem.title')
  protected readonly url = 'product-items'

  getTableSettings(): QTableColParams[] {
    return [{ name: 'id', label: 'Id', field: 'id', sortable: true, align: 'left' }]
  }

  getFormFields(): FormField[] {
    return []
  }

  markSold(id: number): Promise<ProductItemInterface> {
    return api
      .request({
        method: 'post',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/${id}/mark-sold`,
      })
      .then((response) => response.data)
  }
  markNotForSale(id: number): Promise<ProductItemInterface> {
    return api
      .request({
        method: 'post',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/${id}/mark-not-for-sale`,
      })
      .then((response) => response.data)
  }
  rollbackForSaleStatus(id: number): Promise<ProductItemInterface> {
    return api
      .request({
        method: 'post',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/${id}/rollback-for-sale-status`,
      })
      .then((response) => response.data)
  }
}

const modelClass = new ProductItemModel()
export default modelClass

export type { AllItemInterface, IndexItemInterface, GetByIdItemInterface }
