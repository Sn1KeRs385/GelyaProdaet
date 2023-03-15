import { useUserStore } from 'src/stores/user-store'

// eslint-disable-next-line
// @ts-ignore
export default (to, from, next) => {
  const store = useUserStore()
  console.log(store.tokens.accessToken)
  if (!store.tokens.accessToken) {
    next({ name: 'login' })
    return
  }

  next()
}
