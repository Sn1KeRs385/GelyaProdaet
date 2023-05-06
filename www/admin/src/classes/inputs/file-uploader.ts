import { BaseInput } from 'src/classes/inputs/base-input'
import FileUploaderComponent from 'src/components/inputs/FileUploader.vue'
import QFileParams from 'src/interfaces/quasar/q-file-params'
import CollectionName from 'file-uploader/enums/collection-name'

interface Constructor {
  label: string
  fileUploaderOptions?: QFileParams
  collectionName?: CollectionName
  filesField?: string
}

class FileUploader extends BaseInput {
  public readonly label: string
  public readonly fileUploaderOptions?: QFileParams
  public readonly collectionName?: CollectionName
  public readonly filesField?: string
  constructor({ label, fileUploaderOptions, collectionName, filesField }: Constructor) {
    super(FileUploaderComponent)

    this.label = label
    this.fileUploaderOptions = fileUploaderOptions
    this.collectionName = collectionName
    this.filesField = filesField
  }
  public getParams(): unknown {
    return {
      label: this.label,
      fileUploaderOptions: this.fileUploaderOptions,
      collectionName: this.collectionName,
      filesField: this.filesField,
    }
  }
}

export default FileUploader

export type { Constructor }
