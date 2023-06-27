import { defineStore } from 'pinia'
import CompilationInterface from 'src/interfaces/models/compilation-interface'

interface CompilationsStore {
  compilations: CompilationInterface[]
}

export const useCompilationsStore = defineStore('compilations', {
  state: (): CompilationsStore => ({
    compilations: [],
  }),
  getters: {},
  actions: {
    setCompilations(compilations: CompilationInterface[]) {
      this.compilations = compilations
    },
  },
})
