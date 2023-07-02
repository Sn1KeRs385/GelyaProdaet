import { api } from 'src/boot/axios'
import ApiPaginationResponseInterface from 'src/interfaces/Api/pagination-response-interface'
import FormField from 'src/interfaces/admin/form-field'
import QTableColParams from 'src/interfaces/quasar/q-table-params'
import FormTabData from 'src/interfaces/admin/form-tab-data'

interface CreateItemInterface {
  id: number
}
interface UpdateItemInterface {
  id: number
}
interface DeleteItemInterface {
  id: number
}
interface DefaultSortInterface {
  sortBy: string | undefined
  desc: boolean | undefined
}
interface ActionsInterface {
  editButton: boolean
  deleteButton: boolean
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

  getTableDefaultSort(): DefaultSortInterface {
    return {
      sortBy: 'id',
      desc: true,
    }
  }

  getTableActions(): ActionsInterface {
    return {
      editButton: true,
      deleteButton: true,
    }
  }

  getFormTabs(): FormTabData[] {
    return []
  }

  all(): Promise<AllItemInterface[]> {
    return api
      .request({
        method: 'get',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/all`,
      })
      .then((response) => response.data)
  }

  index(
    page = 1,
    perPage = 25,
    orderBy?: string,
    orderDesc?: boolean
  ): Promise<ApiPaginationResponseInterface<IndexItemInterface>> {
    return api
      .request({
        method: 'get',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}`,
        params: {
          page: page,
          per_page: perPage,
          order_by: orderBy,
          order_desc: orderDesc,
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

  update(id: number, data: unknown): Promise<UpdateItemInterface> {
    return api
      .request({
        method: 'put',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/${id}`,
        data: data,
      })
      .then((response) => response.data)
  }

  delete(id: number): Promise<DeleteItemInterface> {
    return api
      .request({
        method: 'delete',
        url: `${this.basePath}/v${this.apiVersion}/${this.url}/${id}`,
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

export type { CreateItemInterface, DefaultSortInterface }
