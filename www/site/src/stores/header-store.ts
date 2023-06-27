import { defineStore } from 'pinia'
import LinkInterface from 'src/interfaces/link-interface'
import HeaderInterface from 'src/interfaces/header-interface'

interface HeaderStore {
  links: LinkInterface[]
}

export const useHeaderStore = defineStore('header', {
  state: (): HeaderStore => ({
    links: [],
  }),
  getters: {},
  actions: {
    set(header: HeaderInterface) {
      this.links = header.links
    },
  },
})
