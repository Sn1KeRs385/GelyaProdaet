<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import AutoFormComponent from 'src/components/models/AutoFormComponent.vue'
import { computed, onBeforeMount, ref } from 'vue'
import { Loading, QSelectOption, QTreeNode, useQuasar } from 'quasar'
import OzonDataModel, { GetByIdItemInterface } from 'src/models/ozon-data'
import FormData from 'src/interfaces/admin/form-data'
import { AxiosError } from 'axios/index'
import ApiErrorInterface from 'src/interfaces/Api/error-interface'
import apiErrorHandler from 'src/utils/api-error-handler'
import { useOzonDataStore } from 'src/stores/ozon-data-store'
import QuasarTree from 'src/classes/inputs/quasar/quasar-tree'
import FormField from 'src/interfaces/admin/form-field'
import QuasarInput from 'src/classes/inputs/quasar/quasar-input'
import Form from 'src/classes/inputs/form'
import QuasarSelect from 'src/classes/inputs/quasar/quasar-select'
interface Props {
  productId: number
}

const props = defineProps<Props>()

const { t } = useI18n()

const $q = useQuasar()

const ozonDataStore = useOzonDataStore()

const modelDataLoading = ref(false)
const modelData = ref<GetByIdItemInterface | undefined>()
const extraFields = ref<FormField[]>([])

onBeforeMount(() => {
  Loading.show()
  modelDataLoading.value = true
  OzonDataModel.getByProductId(props.productId).then((response) => {
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

  data.product_id = props.productId

  OzonDataModel.create(data)
    .then(() => {
      $q.notify({
        progress: true,
        type: 'positive',
        position: 'top-right',
        message: 'Настройки сохранены',
      })
    })
    .catch((error: AxiosError<ApiErrorInterface>) => {
      apiErrorHandler(error, { formFields: formData })
    })
}

const onUpdateCategory = async (node: QTreeNode | undefined) => {
  extraFields.value.splice(0, extraFields.value.length)
  if (!node) {
    return
  }

  const tempFields: FormField[] = []

  const attributes = await OzonDataModel.getAttributesByCategory(node.category_id)
  const values: { [key: number]: QSelectOption<string | number>[] } = {}
  const promises = attributes
    .filter(({ dictionary_id }) => dictionary_id !== 0)
    .map((attribute) => {
      return OzonDataModel.getAttributeValues(node.category_id, attribute.id).then((response) => {
        values[attribute.id] = response.map((value): QSelectOption<string | number> => {
          return {
            label: value.value,
            value: `${value.id}|${value.value}`,
          }
        })
      })
    })

  await Promise.all(promises)

  attributes.forEach((attribute) => {
    if (attribute.dictionary_id === 0) {
      tempFields.push({
        key: attribute.id.toString(),
        input: new QuasarInput({
          tooltip: attribute.description,
          label: attribute.name,
          required: attribute.is_required,
        }),
      })
    } else {
      tempFields.push({
        key: attribute.id.toString(),
        input: new QuasarSelect({
          label: attribute.name,
          tooltip: attribute.description,
          multiple: attribute.is_collection,
          required: attribute.is_required,
          optionsCallback: () => values[attribute.id],
        }),
      })
    }
  })

  extraFields.value.push({
    key: 'attributes',
    input: new Form({ label: t('models.ozonData.form.attributes.label'), formFields: tempFields }),
  })
}

const baseFields = computed(() => OzonDataModel.getFormFields())

const categoryField = computed<FormField>(() => {
  return {
    key: 'category_id',
    input: new QuasarTree({
      label: 'Категория',
      optionsCallback: () => ozonDataStore.getCategoriesTree(),
      optionSelectedHook: onUpdateCategory,
      params: {
        nodeKey: 'category_id',
      },
    }),
  }
})

const fields = computed(() => {
  return [categoryField.value, ...baseFields.value, ...extraFields.value]
})
</script>

<template>
  <q-card>
    <q-card-section>
      <div class="text-h6">{{ t('models.ozonData.form.title') }}</div>
    </q-card-section>

    <q-card-section class="q-pt-none">
      <auto-form-component
        v-if="!modelDataLoading"
        :fields="fields"
        :model-data="modelData"
        @submit-form="submitForm"
      />
    </q-card-section>

    <q-card-actions align="right">
      <slot name="action-slot" />
    </q-card-actions>
  </q-card>
</template>
