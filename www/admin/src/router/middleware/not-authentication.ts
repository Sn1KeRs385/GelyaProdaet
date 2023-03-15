import { useUserStore } from 'src/stores/user-store'

// eslint-disable-next-line
// @ts-ignore
export default (to, from, next) => {
  const store = useUserStore()

  if (store.tokens.accessToken) {
    next({ name: 'index' })
    return
  }

  next()
}
