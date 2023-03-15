export default interface ApiFileInterface {
  id: number
  status: string
  collection: string
  filename?: string
  type?: string
  url?: string
  files?: ApiFileInterface[]
}
