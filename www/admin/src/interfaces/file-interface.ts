import FileStatus from 'file-uploader/enums/file-status'
import CollectionName from 'file-uploader/enums/collection-name'

export default interface FileInterface {
  id: number
  status: FileStatus
  collection: CollectionName
  filename?: string
  type?: string
  url?: string
  files?: FileInterface[]
}
