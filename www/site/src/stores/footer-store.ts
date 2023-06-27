import { defineStore } from 'pinia'
import LinkInterface from 'src/interfaces/link-interface'
import FooterInterface from 'src/interfaces/footer-interface'

interface FooterStore {
  links: LinkInterface[]
}

export const useFooterStore = defineStore('footer', {
  state: (): FooterStore => ({
    links: [],
  }),
  getters: {},
  actions: {
    set(footer: FooterInterface) {
      this.links = footer.links
    },
  },
})
