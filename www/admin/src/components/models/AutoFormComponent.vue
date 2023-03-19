<script setup lang="ts">
import FormField from 'src/interfaces/admin/form-field'
import FormData from 'src/interfaces/admin/form-data'
import { useI18n } from 'vue-i18n'
import { reactive } from 'vue'

interface Props {
  fields: FormField[]
}

const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'submitForm', formData: FormData): void
}>()

const { t } = useI18n()

const formData = reactive<FormData>({})

const resetForm = () => {
  props.fields.forEach((field) => {
    if (!formData[field.key]) {
      formData[field.key] = {
        value: field.defaultValue,
        errors: [],
      }
    } else {
      formData[field.key].value = field.defaultValue
      formData[field.key].errors.splice(0, formData[field.key].errors.length)
    }
  })
}

const resetErrors = () => {
  props.fields.forEach((field) => {
    formData[field.key].errors.splice(0, formData[field.key].errors.length)
  })
}

const submitForm = () => {
  resetErrors()
  emit('submitForm', formData)
}

resetForm()
</script>

<template>
  <q-form @submit="submitForm" @reset="resetForm">
    {{ formData }}
    <template v-for="field in fields" :key="field.key">
      <component
        :is="field.input.component"
        v-bind="field.input.getParams()"
        v-model="formData[field.key].value"
        :error="formData[field.key].errors.length > 0"
        :error-message="formData[field.key].errors.join('\r\n')"
      ></component>
    </template>

    <div class="row tw-space-x-8px tw-mt-6">
      <q-btn :label="t('models.base.form.submit')" type="submit" color="primary" no-caps />
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
