import { api } from 'src/boot/axios'
import ApiPaginationResponseInterface from 'src/interfaces/Api/pagination-response-interface'
import FormField from 'src/interfaces/admin/form-field'
import QTableColParams from 'src/interfaces/quasar/q-table-params'

interface CreateItemInterface {
  id: number
}

abstract class BaseModel<AllItemInterface, IndexItemInterface, GetByIdItemInterface> {
  protected readonly apiVersion = 1

  public readonly tablePageComponent = () => import('src/pages/models/TablePage.vue')
  public readonly formPageComponent = () => import('src/pages/models/FormPage.vue')
  public readonly viewPageComponent = () => import('src/pages/models/FormPage.vue')
  protected abstract readonly url: string

  protected readonly basePath: string = 'admin'
  protected abstract readonly title: string

  abstract getTableSettings(): QTableColParams[]
  abstract getFormFields(): FormField[]

  getUrl(): string {
    return this.url
  }
  getTitle(): string {
    return this.title
  }
  all(): Promise<AllItemInterface[]> {
    return api
      .request({
        method: 'get',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/all`,
      })
      .then((response) => response.data)
  }
  index(page = 1, perPage = 25): Promise<ApiPaginationResponseInterface<IndexItemInterface>> {
    return api
      .request({
        method: 'get',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}`,
        params: {
          page: page,
          per_page: perPage,
        },
      })
      .then((response) => response.data)
  }
  create(data: unknown): Promise<CreateItemInterface> {
    return api
      .request({
        method: 'post',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}`,
        data: data,
      })
      .then((response) => response.data)
  }

  update(id: number, data: unknown): Promise<null> {
    return api
      .request({
        method: 'put',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/${id}`,
        data: data,
      })
      .then((response) => response.data)
  }
  getById(id: number): Promise<GetByIdItemInterface> {
    return api
      .request({ method: 'get', url: `${this.basePath}/v${this.apiVersion}/${this.url}/${id}` })
      .then((response) => response.data)
  }
}

export default BaseModel

export type { CreateItemInterface }
