import { api } from 'src/boot/axios'
import ApiPaginationResponseInterface from 'src/interfaces/Api/pagination-response-interface'
import { AxiosResponse } from 'axios'
import FormField from 'src/interfaces/admin/form-field'
import QTableColParams from 'src/interfaces/quasar/q-table-params'

interface CreateItemInterface {
  id: number
}

const basePath = 'admin'
abstract class BaseModel<AllItemInterface, IndexItemInterface> {
  protected apiVersion = 1
  protected abstract readonly url: string
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
        url: `${basePath}/v${this.apiVersion}/${this.url}/all`,
      })
      .then((response: AxiosResponse<AllItemInterface[]>) => {
        return response.data
      })
  }
  index(page = 1, perPage = 25): Promise<ApiPaginationResponseInterface<IndexItemInterface>> {
    return api
      .request({
        method: 'get',
        url: `${basePath}/v${this.apiVersion}/${this.url}`,
        params: {
          page: page,
          per_page: perPage,
        },
      })
      .then((response: AxiosResponse<ApiPaginationResponseInterface<IndexItemInterface>>) => {
        return response.data
      })
  }
  create(data: unknown): Promise<CreateItemInterface> {
    return api
      .request({
        method: 'post',
        url: `${basePath}/v${this.apiVersion}/${this.url}`,
        data: data,
      })
      .then((response: AxiosResponse<CreateItemInterface>) => {
        return response.data
      })
  }
}

export default BaseModel

export type { CreateItemInterface }
