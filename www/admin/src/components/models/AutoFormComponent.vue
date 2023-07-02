<script setup lang="ts">
import FormField, { BaseInputOrCallback } from 'src/interfaces/admin/form-field'
import FormData from 'src/interfaces/admin/form-data'
import { useI18n } from 'vue-i18n'
import { computed, reactive, watch } from 'vue'
import { BaseInput } from 'src/classes/inputs/base-input'
import ApiFileInterface from 'src/interfaces/Api/file-interface'
import BaseModelInterface from 'src/interfaces/models/base-model-interface'

interface Props {
  fields: FormField[]
  modelData?: BaseModelInterface
  tab?: string
  hideButtons?: boolean
  globalFiles?: ApiFileInterface[]
  autoSave?: boolean
}

const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'submitForm', formData: FormData): void
}>()

const { t } = useI18n()

const formData = reactive<FormData>({})
const formReadyComponents = reactive<{ [key: string]: boolean }>({})

if (props.autoSave) {
  watch(
    formData,
    () => {
      submitForm()
    },
    { deep: true }
  )
}

const isEditForm = computed(() => !!props.modelData)
const fieldsShowed = computed(() =>
  props.fields.filter(
    (field) =>
      !((field.hideInCreate && !isEditForm.value) || (field.hideInUpdate && isEditForm.value))
  )
)

const resetForm = () => {
  fieldsShowed.value.forEach((field) => {
    formReadyComponents[field.key] = true
    let value: unknown = field.defaultValue
    if (props.modelData?.hasOwnProperty(field.key)) {
      value = props.modelData?.[field.key]
    }
    if (!formData[field.key]) {
      formData[field.key] = {
        // eslint-disable-next-line
        // @ts-ignore
        value: value,
        errors: [],
      }
    } else {
      formData[field.key].value = value
      formData[field.key].errors.splice(0, formData[field.key].errors.length)
    }
  })
}

const resetErrors = () => {
  fieldsShowed.value.forEach((field) => {
    formData[field.key].errors.splice(0, formData[field.key].errors.length)
  })
}

const submitForm = () => {
  resetErrors()
  emit('submitForm', formData)
}

resetForm()

const formReady = computed(() => {
  let ready = true
  Object.values(formReadyComponents).forEach((readyComponent) => {
    if (!readyComponent) {
      ready = false
    }
  })

  return ready
})

const getInput = computed(() => (input: BaseInputOrCallback) => {
  if (input instanceof BaseInput) {
    return input
  }

  return input(props.modelData)
})
</script>

<template>
  <q-form @submit="submitForm" @reset="resetForm">
    <template v-for="field in fields" :key="field.key">
      <component
        :is="getInput(field.input).component"
        v-if="isEditForm ? !field.hideInUpdate : !field.hideInCreate"
        v-bind="getInput(field.input).getParams()"
        v-model="formData[field.key].value"
        v-model:is-ready="formReadyComponents[field.key]"
        :field-key="field.key"
        :model-data="modelData"
        :global-files="modelData?.files || props.globalFiles"
        :error="formData[field.key].errors.length > 0"
        :error-message="formData[field.key].errors.join('\r\n')"
        :style="{ display: !tab || tab === field.tabName ? undefined : 'none !important' }"
        class="tw-mt-6px md:tw-mt-12px lg:tw-mt-18px"
      />
    </template>

    <div v-if="!hideButtons" class="row tw-space-x-8px tw-mt-6">
      <q-btn
        :label="isEditForm ? t('models.base.form.save') : t('models.base.form.submit')"
        type="submit"
        color="primary"
        no-caps
        :disable="!formReady"
      />
      <q-btn
        :label="t('models.base.form.reset')"
        type="reset"
        color="primary"
        flat
        no-caps
        class="q-ml-sm"
      />
    </div>
  </q-form>
</template>
