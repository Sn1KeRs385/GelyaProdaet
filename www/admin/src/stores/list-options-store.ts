import { defineStore } from 'pinia'
import Option from 'src/interfaces/option'
import ListOption from 'src/models/list-option'
import OptionGroupSlug from 'src/enums/option-group-slug'
import { t } from 'src/boot/i18n'

interface ListOptionsStore {
  options: Option[]
  groupSlugsHuman: { [key in OptionGroupSlug]?: string }
}

export const useListOptionsStore = defineStore('list-options', {
  state: (): ListOptionsStore => ({
    options: [],
    groupSlugsHuman: {},
  }),
  getters: {},
  actions: {
    getHumanSlugFromBackend(slug: OptionGroupSlug): string | undefined {
      return this.groupSlugsHuman[slug]
    },
    getHumanSlugFromLocale(slug: OptionGroupSlug): string {
      return t(`enums.optionGroupSlugHuman.${slug}`)
    },
    getHumanSlug(slug: OptionGroupSlug): string {
      return this.getHumanSlugFromBackend(slug) || this.getHumanSlugFromLocale(slug)
    },
    loadOptions() {
      ListOption.all().then((response) => {
        this.options.splice(
          0,
          this.options.length,
          ...response.map((option) => {
            const groupSlugAsEnum = option.group_slug as OptionGroupSlug
            if (!this.groupSlugsHuman[groupSlugAsEnum]) {
              this.groupSlugsHuman[groupSlugAsEnum] = option.group_slug_human
            }

            return {
              id: option.id,
              groupSlug: groupSlugAsEnum,
              title: option.title,
            }
          })
        )
      })
    },
    getOptionById(id: number): Option | undefined {
      return this.options.find((option) => option.id === id)
    },
    getOptionBySlug(slug: OptionGroupSlug): Option[] {
      return this.options.filter(({ groupSlug }) => groupSlug === slug)
    },
    getSelectMappedOptionBySlug(slug: OptionGroupSlug) {
      return this.getOptionBySlug(slug).map((option) => ({ value: option.id, label: option.title }))
    },
  },
})
