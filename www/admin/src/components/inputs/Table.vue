<script setup lang="ts">
import QTableColParams from 'src/interfaces/quasar/q-table-params'
import FormField from 'src/interfaces/admin/form-field'
import FormData from 'src/interfaces/admin/form-data'
import { useI18n } from 'vue-i18n'
import { ref } from 'vue'
import AutoFormComponent from 'src/components/models/AutoFormComponent.vue'

interface Props {
  modelValue?: unknown[]
  label: string
  columns: QTableColParams[]
  formFields: FormField[]
}

const props = defineProps<Props>()

const emit = defineEmits<{ (e: 'update:modelValue', value: unknown[]): void }>()

const { t } = useI18n()

const showAddDialog = ref(false)

const onFormSubmit = (formData: FormData) => {
  showAddDialog.value = false
  const newValue = [...(props.modelValue || [])]

  const data: { [key: string]: unknown } = {}

  Object.entries(formData).forEach(([key, value]) => {
    data[key] = value.value
  })

  newValue.push(data)

  emit('update:modelValue', newValue)
}
</script>

<template>
  <div>
    <q-table
      color="primary"
      :title="label"
      :columns="columns"
      :row-key="columns[0].field"
      :rows="modelValue"
    >
      <template #top-right>
        <q-btn color="primary" no-caps unelevated @click="showAddDialog = true">
          {{ t('models.base.add') }}
        </q-btn>
      </template>
    </q-table>

    <q-dialog v-model="showAddDialog" position="bottom">
      <q-card>
        <q-card-section>
          <div class="row justify-between">
            <h6 class="tw-my-0 tw-font-semibold">{{ label }} - {{ t('models.base.addModel') }}</h6>
          </div>
          <auto-form-component :fields="formFields" @submit-form="onFormSubmit" />
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>
