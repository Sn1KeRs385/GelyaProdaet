import BaseModel from 'src/models/base-model'
import QTableColParams from 'src/interfaces/quasar/q-table-params'
import FormField from 'src/interfaces/admin/form-field'
import { t } from 'src/boot/i18n'
import QuasarInput from 'src/classes/inputs/quasar/quasar-input'
import { api } from 'src/boot/axios'
import OzonDataInterface from 'src/interfaces/models/ozon-data-interface'

interface AllItemInterface {
  id: number
}
interface IndexItemInterface {
  id: number
}
type GetByIdItemInterface = OzonDataInterface
interface OzonCategory {
  category_id: number
  title: string
  children: OzonCategory[]
}
interface OzonAttribute {
  id: number
  name: string
  description: string
  type: string
  is_collection: boolean
  is_required: boolean
  group_id: number
  group_name: string
  dictionary_id: number
  is_aspect: boolean
  category_dependent: boolean
}
interface OzonAttributeValue {
  id: number
  value: string
  info: string
  picture: string
}

class OzonDataModel extends BaseModel<AllItemInterface, IndexItemInterface, GetByIdItemInterface> {
  protected readonly title = t('models.ozonData.title')
  protected readonly url = 'ozon-data'

  getTableSettings(): QTableColParams[] {
    return []
  }

  getFormFields(): FormField[] {
    return [
      {
        key: 'dept',
        input: new QuasarInput({
          label: t('models.ozonData.form.dept.label'),
        }),
      },
      {
        key: 'height',
        input: new QuasarInput({ label: t('models.ozonData.form.height.label') }),
      },
      {
        key: 'width',
        input: new QuasarInput({ label: t('models.ozonData.form.width.label') }),
      },
      {
        key: 'weight',
        input: new QuasarInput({ label: t('models.ozonData.form.weight.label') }),
      },
    ]
  }

  getByProductId(id: number): Promise<GetByIdItemInterface | undefined> {
    return api
      .request({
        method: 'get',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/get-by-product-id/${id}`,
      })
      .then((response) => response.data)
  }

  getCategories(): Promise<OzonCategory[]> {
    return api
      .request({
        method: 'get',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/categories`,
      })
      .then((response) => response.data)
  }

  getAttributesByCategory(categoryId: number): Promise<OzonAttribute[]> {
    return api
      .request({
        method: 'get',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/category/${categoryId}/attributes`,
      })
      .then((response) => response.data)
  }

  getAttributeValues(categoryId: number, attributeId: number): Promise<OzonAttributeValue[]> {
    return api
      .request({
        method: 'get',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/category/${categoryId}/attribute/${attributeId}/values`,
      })
      .then((response) => response.data)
  }
}

const modelClass = new OzonDataModel()
export default modelClass

export type { AllItemInterface, IndexItemInterface, GetByIdItemInterface }
