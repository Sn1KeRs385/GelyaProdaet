import { defineStore } from 'pinia'
import FileUploadInfoInterface from 'file-uploader/interfaces/file-upload-info-interface'
import CollectionName from 'file-uploader/enums/collection-name'
import { uploadFile } from 'file-uploader/utils/file-uploader'

interface FileUploadStoreInterface {
  files: FileUploadInfoInterface[]
}

export const useFileUploadStore = defineStore('fileUpload', {
  state: (): FileUploadStoreInterface => ({
    files: [],
  }),
  actions: {
    addFileToUploadQueue(file: File, collectionName: CollectionName): FileUploadInfoInterface {
      const FileUploadInfo = {
        uploadStarted: false,
        progress: 0,
        file,
        collectionName,
        bytesPerRequest: 1024 * 1024,
        errors: [],
      }
      this.files.push(FileUploadInfo)

      return FileUploadInfo
    },
    addFilesToUploadQueue(
      files: File[],
      collectionName: CollectionName
    ): FileUploadInfoInterface[] {
      return files.map((file) => this.addFileToUploadQueue(file, collectionName))
    },
    async startUpload(): Promise<void> {
      const promises = []
      for (const file of this.files.filter((file) => !file.id && file.errors.length === 0)) {
        promises.push(uploadFile(file))
      }
      await Promise.all(promises)
    },
    removeFileFromList(file: FileUploadInfoInterface) {
      if (file.progress !== 100) {
        return
      }
      this.files = this.files.filter((_file) => _file !== file)
    },
  },
})
