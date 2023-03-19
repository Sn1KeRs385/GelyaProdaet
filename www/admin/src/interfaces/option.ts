import OptionGroupSlug from 'src/enums/option-group-slug'

export default interface Option {
  id: number
  groupSlug: OptionGroupSlug
  title: string
}
