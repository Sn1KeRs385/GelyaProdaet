import { defineStore } from 'pinia'
import UserInterface from 'src/interfaces/user-interface'
import TokenInterface from 'src/interfaces/token-interface'
import ApiTokensInterface from 'src/interfaces/Api/token-interface'
import { router } from 'src/router'
import { clearAuthorizationHeader, setAuthorizationHeader } from 'src/boot/axios'
import { useListOptionsStore } from 'src/stores/list-options-store'

interface UserStoreInterface {
  user?: UserInterface
  tokens: TokenInterface
}

const storageUserTokens = localStorage.getItem('userTokens')

export const useUserStore = defineStore('user', {
  state: (): UserStoreInterface => ({
    user: undefined,
    tokens: storageUserTokens
      ? JSON.parse(storageUserTokens)
      : {
          accessToken: undefined,
        },
  }),
  getters: {},
  actions: {
    saveTokens(tokens: ApiTokensInterface) {
      this.tokens.accessToken = tokens.access_token
      localStorage.setItem('userTokens', JSON.stringify(this.tokens))

      setAuthorizationHeader(this.tokens.accessToken)

      useListOptionsStore().loadOptions()
    },
    logout() {
      this.user = undefined
      this.tokens.accessToken = undefined
      localStorage.removeItem('userTokens')

      clearAuthorizationHeader()

      router.push({
        name: 'login',
      })
    },
  },
})
