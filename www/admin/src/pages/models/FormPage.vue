<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import BaseModel from 'src/models/base-model'
import { computed, onBeforeMount, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import apiErrorHandler from 'src/utils/api-error-handler'
import FormData from 'src/interfaces/admin/form-data'
import AutoFormComponent from 'src/components/models/AutoFormComponent.vue'
import { AxiosError } from 'axios'
import ApiErrorInterface from 'src/interfaces/Api/error-interface'
import BaseModelInterface from 'src/interfaces/models/base-model-interface'
import { Loading } from 'quasar'
import { useListOptionsStore } from 'src/stores/list-options-store'

interface Props {
  model: BaseModel<BaseModelInterface, BaseModelInterface, BaseModelInterface>
}

const props = defineProps<Props>()

const modelDataLoading = ref(false)
const modelData = ref<BaseModelInterface | undefined>()

const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const listOptionsStore = useListOptionsStore()

const listOptions = ref(listOptionsStore.options)

const tabs = computed(() => props.model.getFormTabs())
const activeTab = ref(tabs.value?.[0]?.name || undefined)

const fields = computed(() => props.model.getFormFields())

onBeforeMount(() => {
  const id = route.params.id
  if (!id || Array.isArray(id)) {
    return
  }

  Loading.show()
  modelDataLoading.value = true
  props.model.getById(parseInt(id)).then((response) => {
    modelData.value = response
    modelDataLoading.value = false
    Loading.hide()
  })
})
const submitForm = (formData: FormData) => {
  const data: { [key: string]: unknown } = {}

  Object.entries(formData).forEach(([key, value]) => {
    data[key] = value.value
  })

  if (modelData.value) {
    const id = modelData.value.id
    props.model
      .update(id, data)
      .then(() => {
        router.push({
          name: `view_${props.model.constructor.name}`,
          params: { id },
        })
      })
      .catch((error: AxiosError<ApiErrorInterface>) => {
        apiErrorHandler(error, { formFields: formData })
      })
  } else {
    props.model
      .create(data)
      .then(() => {
        router.push({ name: `table_${props.model.constructor.name}` })
      })
      .catch((error: AxiosError<ApiErrorInterface>) => {
        apiErrorHandler(error, { formFields: formData })
      })
  }
}
</script>

<template>
  <q-page v-if="!modelDataLoading && listOptions.length > 0" padding>
    <div class="row justify-between tw-mb-6">
      <h4 class="tw-my-0 tw-font-bold">
        {{ model.getTitle() }} -
        {{ modelData ? t('models.base.updateModel') : t('models.base.createModel') }}
      </h4>
      <q-btn color="secondary" no-caps unelevated @click="router.go(-1)">
        {{ t('models.base.back') }}
      </q-btn>
    </div>
    <q-tabs
      v-if="tabs.length > 0"
      v-model="activeTab"
      inline-label
      outside-arrows
      mobile-arrows
      class="bg-primary text-white shadow-2"
    >
      <q-tab
        v-for="tab in tabs"
        :key="tab.name"
        :name="tab.name"
        :icon="tab.icon"
        :label="tab.label"
      />
    </q-tabs>
    <auto-form-component
      :fields="fields"
      :tab="activeTab"
      :model-data="modelData"
      @submit-form="submitForm"
    />
  </q-page>
</template>
