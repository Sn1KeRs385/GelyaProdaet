import { api } from 'src/boot/axios'
import ApiPaginationResponseInterface from 'src/interfaces/Api/pagination-response-interface'
import QTableColParams from 'src/interfaces/quasar/q-table-params'
import { AxiosResponse } from 'axios'

const basePath = 'admin'
abstract class BaseModel<IndexItemInterface> {
  protected apiVersion = 1
  protected abstract readonly url: string
  protected abstract readonly title: string

  abstract getTableSettings(): QTableColParams[]

  getUrl(): string {
    return this.url
  }
  getTitle(): string {
    return this.title
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
  // methods: {
  //   index: (page = 1, perPage = 25) => {
  //     return api.get()
  //   },
  //   create: () => {
  //     console.log('create')
  //   },
  // },
}

export default BaseModel
