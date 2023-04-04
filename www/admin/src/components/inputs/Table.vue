<script setup lang="ts">
import QTableColParams from 'src/interfaces/quasar/q-table-params'
import FormData from 'src/interfaces/admin/form-data'
import { useI18n } from 'vue-i18n'
import { computed, ref } from 'vue'
import AutoFormComponent from 'src/components/models/AutoFormComponent.vue'
import BaseModelInterface from 'src/interfaces/models/base-model-interface'
import BaseModel from 'src/models/base-model'

interface ModelValueInterface {
  id?: number
  [key: string]: unknown
}

interface Props {
  modelValue?: ModelValueInterface[]
  label: string
  model: BaseModel<BaseModelInterface, BaseModelInterface, BaseModelInterface>
  columnsDelete?: string[]
}

const props = defineProps<Props>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: ModelValueInterface[]): void
}>()

const { t } = useI18n()

const showForm = ref(false)
const rowEditData = ref<BaseModelInterface | undefined>()
const rowEditIndex = ref<number | undefined>()
const deletedRows = ref<ModelValueInterface[]>([])
const tableRows = computed<ModelValueInterface[]>(() => [
  ...(props.modelValue || []),
  ...deletedRows.value,
])

const tableDefaultSort = computed(() => props.model.getTableDefaultSort())

const sortMethod = (rows: ModelValueInterface[], sortBy: string, descending: boolean) => {
  const colSetting = columns.value.find(({ name }) => name === sortBy)

  if (!colSetting) {
    return rows
  }

  const getValue = (row: ModelValueInterface) => {
    let value: unknown

    if (typeof colSetting.field === 'string') {
      value = row[colSetting.field]
    } else {
      value = colSetting.field(row)
    }

    if (colSetting.format) {
      return colSetting.format(value, row)
    }

    return value
  }

  return rows.sort((rowA, rowB) => {
    let valueA = descending ? getValue(rowB) : getValue(rowA)
    let valueB = descending ? getValue(rowA) : getValue(rowB)

    if (typeof valueA === 'string' && typeof valueB === 'string') {
      const valA = valueA.toLowerCase()
      const valB = valueB.toLowerCase()
      if (valA < valB) {
        return -1
      }
      if (valA > valB) {
        return 1
      }
    } else if (typeof valueA === 'number' && typeof valueB === 'number') {
      return valueA - valueB
    }

    return 0
  })
}

const pagination = ref({
  sortBy: tableDefaultSort.value.sortBy,
  descending: tableDefaultSort.value.desc,
  page: 1,
  rowsPerPage: 0,
})

const onFormSubmit = (formData: FormData) => {
  showForm.value = false

  if (rowEditIndex.value !== undefined) {
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

const actionSettings = computed(() => props.model.getTableActions())

const columns = computed(() => {
  let settings: QTableColParams[] = props.model
    .getTableSettings()
    .map((setting) => ({ ...setting, sortable: true }))

  const columnsDelete = props.columnsDelete
  if (columnsDelete && columnsDelete.length > 0) {
    settings = settings.filter(
      (setting) => typeof setting.field !== 'string' || !columnsDelete.includes(setting.field)
    )
  }

  if (actionSettings.value.deleteButton || props.modelValue?.find((el) => !el.id)) {
    settings.push({
      name: 'actions',
      label: t('models.base.table.actions.label'),
      field: 'actions',
      format: () => '-',
      align: 'right',
    })
  }

  return settings
})

const formFields = computed(() => props.model.getFormFields())

const onRowClick = (row: BaseModelInterface, index: number) => {
  if (rowIsDeleted.value(row)) {
    return
  }
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

const rowIsDeleted = computed(
  () => (row: ModelValueInterface) =>
    deletedRows.value.findIndex((element) => element === row) !== -1
)

const deleteActionClick = (row: ModelValueInterface) => {
  deletedRows.value.push(row)
  const newModelValue = props.modelValue?.filter((element) => element !== row) || []
  emit('update:modelValue', newModelValue)
}

const restoreActionClick = (row: ModelValueInterface) => {
  const search = deletedRows.value.find((element) => element === row)
  const findIndex = deletedRows.value.findIndex((element) => element === row)

  if (findIndex !== -1) {
    deletedRows.value.splice(findIndex, 1)
  }

  if (search) {
    const newModelValue = [...(props.modelValue || [])]
    newModelValue.push(search)
    emit('update:modelValue', newModelValue)
  }
}
</script>

<template>
  <div>
    <q-table
      v-model:pagination="pagination"
      color="primary"
      :title="label"
      :columns="columns"
      :row-key="columns[0].field || ''"
      :rows="tableRows"
      :rows-per-page-options="[0]"
      binary-state-sort
      :sort-method="sortMethod"
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
          <q-td
            v-for="colSetting in columns"
            :key="colSetting.name"
            :props="tableBodyProps"
            :class="{ 'bg-red': rowIsDeleted(tableBodyProps.row) }"
          >
            <template v-if="colSetting.name === 'actions'">
              <q-btn-group v-if="actionSettings.deleteButton || !tableBodyProps.row.id">
                <q-btn
                  v-if="rowIsDeleted(tableBodyProps.row)"
                  color="primary"
                  icon="settings_backup_restore"
                  @click.stop="restoreActionClick(tableBodyProps.row)"
                />
                <q-btn
                  v-else
                  color="primary"
                  icon="delete_forever"
                  @click.stop="deleteActionClick(tableBodyProps.row)"
                />
              </q-btn-group>
            </template>
            <template v-else>
              {{ getRowValue(colSetting, tableBodyProps.row) }}
            </template>
          </q-td>
        </q-tr>
      </template>
    </q-table>

    <q-dialog v-model="showForm" position="bottom">
      <q-card>
        <q-card-section>
          <div class="row justify-between">
            <h6 v-if="rowEditData" class="tw-my-0 tw-font-semibold">
              {{ label }} - {{ t('models.base.edit') }}
            </h6>
            <h6 v-else class="tw-my-0 tw-font-semibold">
              {{ label }} - {{ t('models.base.addModel') }}
            </h6>
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
