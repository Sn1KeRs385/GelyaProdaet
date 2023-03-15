import { AxiosError } from 'axios'
import { Notify } from 'quasar'
import { t } from 'src/boot/i18n'
import ApiErrorInterface from 'src/interfaces/Api/error-interface'

interface ExtraParamsObject {
  formFields: { [key: string]: { errors: string[] } }
}

export default (
  error: AxiosError<ApiErrorInterface>,
  { formFields }: ExtraParamsObject = { formFields: {} }
) => {
  let message = t('axios.undefinedErrorRetryLate')

  Object.keys(formFields).forEach((fieldKey) => {
    formFields[fieldKey].errors = []
  })

  if (error.response?.data.errors) {
    const errors: string[] = []
    Object.keys(error.response.data.errors).forEach((errorKey) => {
      const errorsComputed: string[] = error.response?.data?.errors?.[errorKey] || []
      if (errorsComputed.length === 0) {
        errorsComputed.push(t('axios.undefinedError'))
      }
      if (formFields[errorKey]) {
        formFields[errorKey].errors = errorsComputed
      } else {
        errors.push(...errorsComputed)
      }
    })
    message = errors.join('\r\n')
  } else if (error.response?.data.message) {
    message = error.response?.data.message
  }

  if (message) {
    Notify.create({
      color: 'negative',
      textColor: 'message',
      icon: 'report_problem',
      message,
      position: 'top-right',
      timeout: 5000,
      progress: true,
    })
  }
}
