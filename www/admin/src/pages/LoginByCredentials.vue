<script setup lang="ts">
import { reactive } from 'vue'
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
  login: {
    value: string | null
    errors: string[]
  }
  password: {
    value: string | null
    hidden: boolean
    errors: string[]
  }
}

const formData = reactive<FormDataInterface>({
  login: {
    value: null,
    errors: [],
  },
  password: {
    value: null,
    hidden: true,
    errors: [],
  },
})

const login = async () => {
  await api
    .post(apiRoutes.auth.getTokensByCredentials, {
      login: formData.login.value,
      password: formData.password.value,
    })
    .then((response) => {
      userStore.saveTokens(response.data)
      if (router.currentRoute.value.redirectedFrom) {
        router.push(router.currentRoute.value.redirectedFrom)
      } else {
        router.push({
          name: 'index',
        })
      }
    })
    .catch((error) => {
      apiErrorHandler(error, { formFields: formData })
    })
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
                v-model="formData.login.value"
                dense
                :label="t('login.form.login.label')"
                :error="formData.password.errors.length > 0"
                :error-message="formData.password.errors.join('\r\n')"
              />
              <q-input
                v-model="formData.password.value"
                :type="formData.password.hidden ? 'password' : 'text'"
                class="q-mt-md"
                dense
                :label="t('login.form.password.label')"
                :error="formData.password.errors.length > 0"
                :error-message="formData.password.errors.join('\r\n')"
              >
                <template #append>
                  <q-icon
                    :name="formData.password.hidden ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="formData.password.hidden = !formData.password.hidden"
                  />
                </template>
              </q-input>
              <div class="column justify-center items-center tw-mt-20px">
                <q-btn color="primary" size="md" :label="t('login.form.enter')" @click="login" />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-page>
    </q-page-container>
  </q-layout>
</template>
