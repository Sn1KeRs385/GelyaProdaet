<script setup lang="ts">
import AutoFormComponent from 'src/components/models/AutoFormComponent.vue'
import BaseModelInterface from 'src/interfaces/models/base-model-interface'
import FormField from 'src/interfaces/admin/form-field'
import { computed } from 'vue'
import ApiFileInterface from 'src/interfaces/Api/file-interface'
import FormData from 'src/interfaces/admin/form-data'

interface ModelValueInterface {
  [key: string]: unknown
}

interface Props {
  fieldKey: string
  modelValue?: ModelValueInterface
  label?: string
  formFields: FormField[]
  modelData?: BaseModelInterface
  globalFiles?: ApiFileInterface[]
}

const props = defineProps<Props>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: ModelValueInterface): void
}>()

const modelDataByKey = computed(() =>
  props.modelData ? (props.modelData?.[props.fieldKey] as BaseModelInterface) : undefined
)

const submitForm = (formData: FormData) => {
  const data: { [key: string]: unknown } = {}

  Object.entries(formData).forEach(([key, value]) => {
    data[key] = value.value
  })

  emit('update:modelValue', data)
}
</script>

<template>
  <q-card bordered>
    <q-item class="text-h6">
      {{ label }}
    </q-item>
    <q-separator />
    <q-card-section>
      <auto-form-component
        :fields="formFields"
        :model-data="modelDataByKey"
        :global-files="globalFiles"
        auto-save
        hide-buttons
        @submit-form="submitForm"
      />
    </q-card-section>
  </q-card>
</template>
