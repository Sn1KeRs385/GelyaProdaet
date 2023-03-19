import { BaseInput } from 'src/classes/inputs/base-input'
import FileUploaderComponent from 'src/components/inputs/FileUploader.vue'
import QFileParams from 'src/interfaces/quasar/q-file-params'

interface Constructor {
  label: string
  fileUploaderOptions?: QFileParams
}

class FileUploader extends BaseInput {
  public readonly label: string
  public readonly fileUploaderOptions?: QFileParams
  constructor({ label, fileUploaderOptions }: Constructor) {
    super(FileUploaderComponent)

    this.label = label
    this.fileUploaderOptions = fileUploaderOptions
  }
  public getParams(): unknown {
    return {
      label: this.label,
      fileUploaderOptions: this.fileUploaderOptions,
    }
  }
}

export default FileUploader

export type { Constructor }
