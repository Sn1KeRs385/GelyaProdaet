import { boot } from 'quasar/wrappers'
import { createI18n } from 'vue-i18n'

import messages from 'src/locale'

export type MessageLanguages = keyof typeof messages
export type MessageSchema = (typeof messages)['ru']

// See https://vue-i18n.intlify.dev/guide/advanced/typescript.html#global-resource-schema-type-definition
/* eslint-disable @typescript-eslint/no-empty-interface */
declare module 'vue-i18n' {
  // define the locale messages schema
  export interface DefineLocaleMessage extends MessageSchema {}

  // define the datetime format schema
  export interface DefineDateTimeFormat {}

  // define the number format schema
  export interface DefineNumberFormat {}
}
/* eslint-enable @typescript-eslint/no-empty-interface */

const i18n = createI18n({
  locale: 'ru',
  fallbackLocale: 'ru',
  legacy: false,
  availableLocales: ['ru'],
  messages,
})

const t = i18n.global.t

export default boot(({ app }) => {
  // Set i18n instance on app
  app.use(i18n)
})

export { i18n, t }
