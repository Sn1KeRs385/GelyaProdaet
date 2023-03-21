import CollectionName from 'file-uploader/enums/collection-name'

export default interface FileUploadInfoInterface {
  uploadStarted: boolean
  id?: number
  progress: number
  isFinishedUpload: boolean
  file: File
  collectionName: CollectionName
  bytesPerRequest: number
  errors: string[]
}
