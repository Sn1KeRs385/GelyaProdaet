import ApiFileInterface from 'src/interfaces/Api/file-interface'

export default interface ModelWithFilesInterface {
  files: ApiFileInterface[]
  [key: string]: ApiFileInterface[]
}
