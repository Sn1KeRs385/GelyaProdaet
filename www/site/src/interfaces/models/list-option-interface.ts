import FileInterface from 'src/interfaces/models/file-interface'

export default interface ListOptionInterface {
  id: number
  group_slug: string
  group_slug_human?: string
  title: string
  logo?: FileInterface[]
  weight: number
}
