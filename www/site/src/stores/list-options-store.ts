import { defineStore } from 'pinia'
import OptionGroupSlug from 'src/enums/option-group-slug'
import { t } from 'src/boot/i18n'
import ListOptionInterface from 'src/interfaces/models/list-option-interface'

interface ListOptionsStore {
  options: ListOptionInterface[]
  optionsForPage: ListOptionInterface[]
  groupSlugsHuman: { [key in OptionGroupSlug]?: string }
}

export const useListOptionsStore = defineStore('list-options', {
  state: (): ListOptionsStore => ({
    options: [],
    optionsForPage: [],
    groupSlugsHuman: {},
  }),
  getters: {},
  actions: {
    setOptions(options: ListOptionInterface[]) {
      this.options = options
      options.forEach((option) => this.writeOptionToLocale(option))
    },
    setOptionsForPage(options: ListOptionInterface[]) {
      this.optionsForPage = options
      options.forEach((option) => this.writeOptionToLocale(option))
    },
    writeOptionToLocale(option: ListOptionInterface) {
      if (!option.group_slug_human) {
        return
      }

      const groupSlugAsEnum = option.group_slug as OptionGroupSlug
      if (!this.groupSlugsHuman[groupSlugAsEnum]) {
        this.groupSlugsHuman[groupSlugAsEnum] = option.group_slug_human
      }
    },
    getHumanSlugFromBackend(slug: OptionGroupSlug): string | undefined {
      return this.groupSlugsHuman[slug]
    },
    getHumanSlugFromLocale(slug: OptionGroupSlug): string {
      return t(`enums.optionGroupSlugHuman.${slug}`)
    },
    getHumanSlug(slug: OptionGroupSlug): string {
      return this.getHumanSlugFromBackend(slug) || this.getHumanSlugFromLocale(slug)
    },
    getOptionById(id: number): ListOptionInterface | undefined {
      return this.options.find((option) => option.id === id)
    },
    getOptionsBySlug(slug: OptionGroupSlug): ListOptionInterface[] {
      return this.options.filter(({ group_slug }) => group_slug === slug)
    },
    getSelectMappedOptionBySlug(slug: OptionGroupSlug) {
      return this.getOptionsBySlug(slug).map((option) => ({
        value: option.id,
        label: option.title,
      }))
    },
  },
})
