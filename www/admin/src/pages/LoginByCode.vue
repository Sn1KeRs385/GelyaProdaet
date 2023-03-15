<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { api } from 'src/boot/axios'
import apiRoutes from 'src/constants/api-routes'
import apiErrorHandler from 'src/utils/api-error-handler'
import { useUserStore } from 'src/stores/user-store'
import { useRouter } from 'vue-router'

const { t } = useI18n()
const userStore = useUserStore()
const router = useRouter()

interface FormDataInterface {
  phone: { value: string | null }
  code: {
    value: string[]
    characterNumber: number
  }
}

const formData = reactive<FormDataInterface>({
  phone: {
    value: null,
  },
  code: {
    value: [],
    characterNumber: 0,
  },
})

const isCodeSent = ref(false)
const codeInputs = ref<InstanceType<typeof HTMLElement>[]>([])

const sendCode = async () => {
  const response = await api
    .post(apiRoutes.auth.sendCode, { phone: `+7${formData.phone.value}` })
    .catch(apiErrorHandler)

  if (!response) {
    return
  }

  const data = response.data

  formData.code.characterNumber = data.character_numbers
  formData.code.value = []

  isCodeSent.value = true

  if (formData.code.value) {
    formData.code.value = data.code.split('')
    await login()
  }
}

const login = async () => {
  const response = await api
    .post(apiRoutes.auth.getTokensByCode, { code: formData.code.value.join('') })
    .catch((error) => {
      formData.code.value = formData.code.value.map(() => '')
      codeInputs.value[0].focus()
      throw error
    })
    .catch(apiErrorHandler)

  if (!response) {
    return
  }

  userStore.saveTokens(response.data)
  if (router.currentRoute.value.redirectedFrom) {
    await router.push(router.currentRoute.value.redirectedFrom)
  } else {
    await router.push({
      name: 'index',
    })
  }
}

const onCodeInput = (value: string, index: number) => {
  if (value.length > 0) {
    formData.code.value[index] = value.slice(-1)
    if (index < codeInputs.value.length - 1) {
      codeInputs.value[index + 1].focus()
    } else {
      login()
    }
  }
}
</script>

<template>
  <q-layout>
    <q-page-container>
      <q-page
        padding
        class="column justify-center items-center tw-bg-gradient-to-r tw-from-indigo-500 tw-via-purple-500 tw-to-pink-500"
      >
        <q-card class="tw-w-full tw-max-w-500px">
          <q-card-section class="bg-primary">
            <h4 class="text-h5 text-white q-my-md">{{ t('login.form.authorization') }}</h4>
          </q-card-section>
          <q-card-section>
            <q-form>
              <q-input
                v-model="formData.phone.value"
                dense
                outlined
                :readonly="isCodeSent"
                mask="+7 (###) ### - ####"
                fill-mask
                unmasked-value
                :label="t('login.form.phoneNumber.label')"
              />
              <div
                v-show="isCodeSent"
                class="row justify-center items-center tw-space-x-10px tw-mt-10px"
              >
                <q-input
                  v-for="index in formData.code.characterNumber"
                  :key="`code_input_${index - 1}`"
                  ref="codeInputs"
                  v-model="formData.code.value[index - 1]"
                  dense
                  outlined
                  class="tw-w-40px"
                  @update:model-value="onCodeInput($event, index - 1)"
                />
              </div>
              <div class="column justify-center items-center tw-mt-20px">
                <q-btn
                  color="positive"
                  size="md"
                  glossy
                  :label="isCodeSent ? t('login.form.sendCodeRetry') : t('login.form.sendCode')"
                  @click="sendCode"
                />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-page>
    </q-page-container>
  </q-layout>
</template>
