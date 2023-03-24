<script setup lang="ts">
import QTableColParams from 'src/interfaces/quasar/q-table-params'
import FormField from 'src/interfaces/admin/form-field'
import FormData from 'src/interfaces/admin/form-data'
import { useI18n } from 'vue-i18n'
import { ref } from 'vue'
import AutoFormComponent from 'src/components/models/AutoFormComponent.vue'
import BaseModelInterface from 'src/interfaces/models/base-model-interface'

interface Props {
  modelValue?: { [key: string]: unknown }[]
  label: string
  columns: QTableColParams[]
  formFields: FormField[]
}

const props = defineProps<Props>()

const emit = defineEmits<{ (e: 'update:modelValue', value: unknown[]): void }>()

const { t } = useI18n()

const showForm = ref(false)
const rowEditData = ref<BaseModelInterface | undefined>()
const rowEditIndex = ref<number | undefined>()

const onFormSubmit = (formData: FormData) => {
  showForm.value = false

  if (rowEditIndex.value !== undefined) {
    console.log(rowEditIndex.value)
    const newValue = [...(props.modelValue || [])]
    const valueForChange = newValue[rowEditIndex.value]
    Object.entries(formData).forEach(([key, value]) => {
      valueForChange[key] = value.value
    })
    newValue[rowEditIndex.value] = valueForChange
    emit('update:modelValue', newValue)
    return
  }

  const newValue = [...(props.modelValue || [])]

  const data: { [key: string]: unknown } = {}

  Object.entries(formData).forEach(([key, value]) => {
    data[key] = value.value
  })

  newValue.push(data)

  emit('update:modelValue', newValue)
}

const onRowClick = (row: BaseModelInterface, index: number) => {
  console.log(row, index)
  rowEditData.value = row
  rowEditIndex.value = index
  showForm.value = true
}
const onAddClick = () => {
  rowEditData.value = undefined
  rowEditIndex.value = undefined
  showForm.value = true
}

const getRowValue = (colSetting: QTableColParams, tableRow: { [key: string]: unknown }) => {
  const value = tableRow[colSetting.field as string]
  if (colSetting.format) {
    return colSetting.format(value, tableRow)
  }

  return value
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
        <q-btn color="primary" no-caps unelevated @click="onAddClick">
          {{ t('models.base.add') }}
        </q-btn>
      </template>
      <template #body="tableBodyProps">
        <q-tr
          :props="tableBodyProps"
          @click="onRowClick(tableBodyProps.row, tableBodyProps.rowIndex)"
        >
          <q-td v-for="colSetting in columns" :key="colSetting.name" :props="tableBodyProps">
            {{ getRowValue(colSetting, tableBodyProps.row) }}
          </q-td>
        </q-tr>
      </template>
    </q-table>

    <q-dialog v-model="showForm" position="bottom">
      <q-card>
        <q-card-section>
          <div class="row justify-between">
            <h6 class="tw-my-0 tw-font-semibold">{{ label }} - {{ t('models.base.addModel') }}</h6>
          </div>
          <auto-form-component
            :fields="formFields"
            :model-data="rowEditData"
            @submit-form="onFormSubmit"
          />
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>
