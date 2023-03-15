import { defineStore } from 'pinia'
import FileInterface from 'src/interfaces/file-interface'
import ApiFileInterface from 'src/interfaces/Api/file-interface'
import { api } from 'src/boot/axios'
import apiRoutes from 'src/constants/api-routes'
import apiErrorHandler from 'src/utils/api-error-handler'
import PaginationResponseInterface from 'src/interfaces/Api/pagination-response-interface'

interface FilesStoreInterface {
  files: FileInterface[]
  page: number
  isLoadingNext: boolean
  nextPageExists: boolean
}

const perPage = 50

export const useFilesStore = defineStore('files', {
  state: (): FilesStoreInterface => ({
    files: [],
    page: 0,

    isLoadingNext: false,
    nextPageExists: true,
  }),
  getters: {},
  actions: {
    async loadNextPage() {
      if (this.isLoadingNext || !this.nextPageExists) {
        return
      }

      this.isLoadingNext = true

      await api
        .get(apiRoutes.v1.files, { params: { page: this.page + 1, per_page: perPage } })
        .then((response) => {
          const data = response.data as PaginationResponseInterface<ApiFileInterface>
          this.page = this.page + 1
          if (data.per_page * data.current_page > data.to) {
            this.nextPageExists = false
          }
          this.files = this.files.concat(data.items as FileInterface[])
        })
        .catch(apiErrorHandler)

      this.isLoadingNext = false
    },
  },
})
