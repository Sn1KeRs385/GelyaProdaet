<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import BaseModel from 'src/models/base-model'
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import apiErrorHandler from 'src/utils/api-error-handler'
import FormData from 'src/interfaces/admin/form-data'
import AutoFormComponent from 'src/components/models/AutoFormComponent.vue'
import { AxiosError } from 'axios'
import ApiErrorInterface from 'src/interfaces/Api/error-interface'

interface Props {
  model: BaseModel<never, never>
}

const props = defineProps<Props>()

const { t } = useI18n()
const router = useRouter()

const fields = computed(() => props.model.getFormFields())

const submitForm = (formData: FormData) => {
  const data: { [key: string]: unknown } = {}

  Object.entries(formData).forEach(([key, value]) => {
    data[key] = value.value
  })

  props.model
    .create(data)
    .then(() => {
      router.push({ name: `table_${props.model.constructor.name}` })
    })
    .catch((error: AxiosError<ApiErrorInterface>) => {
      apiErrorHandler(error, { formFields: formData })
    })
}
</script>

<template>
  <q-page padding>
    <div class="row justify-between">
      <h4 class="tw-my-0 tw-font-bold">
        {{ t('models.base.createModel', { model: model.getTitle() }) }}
      </h4>
      <q-btn color="secondary" no-caps unelevated @click="router.go(-1)">
        {{ t('models.base.back') }}
      </q-btn>
    </div>
    <auto-form-component :fields="fields" @submit-form="submitForm" />
  </q-page>
</template>
