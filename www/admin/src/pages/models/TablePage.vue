<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import BaseModel from 'src/models/base-model'
import { computed, onMounted, ref, watch } from 'vue'
import { QTableProps } from 'quasar'
import { useRoute, useRouter } from 'vue-router'
import QTableColParams from 'src/interfaces/quasar/q-table-params'
import BaseModelInterface from 'src/interfaces/models/base-model-interface'
import apiErrorHandler from 'src/utils/api-error-handler'

interface Props {
  model: BaseModel<BaseModelInterface, BaseModelInterface, BaseModelInterface>
}

const props = defineProps<Props>()

const { t } = useI18n()
const route = useRoute()
const router = useRouter()

const tableRef = ref()
const rows = ref<BaseModelInterface[]>([])
const loading = ref(false)

const tableDefaultSort = computed(() => props.model.getTableDefaultSort())

const pagination = ref({
  sortBy: route.query.sortBy || tableDefaultSort.value.sortBy,
  descending: route.query.desc ? route.query.desc === 'true' : tableDefaultSort.value.desc,
  page: route.query.page || 1,
  rowsPerPage: route.query.perPage || 25,
  rowsNumber: 1,
})
const confirmationShow = ref(false)
const confirmationText = ref('')
const confirmationCallback = ref<() => void>()

const actionSettings = computed(() => props.model.getTableActions())

const columns = computed(() => {
  const settings = props.model.getTableSettings()

  let actionEnabled = false

  Object.values(actionSettings.value).forEach((actionSetting) => {
    if (actionSetting) {
      actionEnabled = true
    }
  })

  if (actionEnabled) {
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

watch(
  () => props.model,
  () => {
    loading.value = true
    rows.value.splice(0, rows.value.length)
    pagination.value.page = 1
    pagination.value.rowsPerPage = 25
    pagination.value.rowsNumber = 0
    pagination.value.sortBy = tableDefaultSort.value.sortBy
    pagination.value.descending = tableDefaultSort.value.desc
    tableRef.value.requestServerInteraction()
  }
)

onMounted(() => {
  tableRef.value.requestServerInteraction()
})

const onRequest = async (tableProps: QTableProps) => {
  loading.value = true

  const tempData = await props.model.index(
    tableProps.pagination?.page || 1,
    tableProps.pagination?.rowsPerPage || 25,
    tableProps.pagination?.sortBy,
    tableProps.pagination?.descending
  )

  rows.value.splice(0, rows.value.length, ...tempData.items)

  pagination.value.page = tempData.current_page
  pagination.value.rowsPerPage = tempData.per_page
  pagination.value.rowsNumber = tempData.total
  pagination.value.sortBy = tableProps.pagination?.sortBy || undefined
  pagination.value.descending =
    tableProps.pagination?.descending !== undefined ? tableProps.pagination?.descending : true

  loading.value = false

  saveParamsToRoute()
}

const editActionClick = (data: BaseModelInterface) => {
  router.push({ path: `/${props.model.getUrl()}/${data.id}/edit` })
}

const deleteActionClick = (data: BaseModelInterface) => {
  confirmationText.value = t('models.base.table.confirmationDelete')
  confirmationShow.value = true
  confirmationCallback.value = () => {
    props.model.delete(data.id).catch(apiErrorHandler)
    tableRef.value.requestServerInteraction()
  }
}

const saveParamsToRoute = () => {
  const params: {
    page: string
    perPage: string
    sortBy: string | undefined
    desc: string | undefined
  } = {
    page: pagination.value.page.toString(),
    perPage: pagination.value.rowsPerPage.toString(),
    sortBy: undefined,
    desc: undefined,
  }

  if (pagination.value.sortBy) {
    params.sortBy = pagination.value.sortBy.toString()
    if (pagination.value.descending !== undefined) {
      params.desc = pagination.value.descending.toString()
    }
  }

  router.push({ query: params })
}

const onRowClick = (row: BaseModelInterface) => {
  router.push({ name: `view_${props.model.constructor.name}`, params: { id: row.id } })
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
  <q-page padding>
    <div class="row justify-between">
      <h4 class="tw-my-0 tw-font-bold">{{ model.getTitle() }}</h4>
      <q-btn
        color="primary"
        no-caps
        unelevated
        :to="{ name: `create_form_${model.constructor.name}` }"
      >
        {{ t('models.base.create') }}
      </q-btn>
    </div>
    <q-table
      ref="tableRef"
      v-model:pagination="pagination"
      class="tw-mt-4"
      color="primary"
      :rows-per-page-options="[25, 50, 75, 100, 200, 500, 1000]"
      :rows="rows"
      :columns="columns"
      row-key="id"
      :loading="loading"
      binary-state-sort
      @request="onRequest"
    >
      <template #body="tableBodyProps">
        <q-tr :props="tableBodyProps" @click="onRowClick(tableBodyProps.row)">
          <q-td v-for="colSetting in columns" :key="colSetting.name" :props="tableBodyProps">
            <template v-if="colSetting.name === 'actions'">
              <q-btn-group>
                <q-btn
                  v-if="actionSettings.deleteButton"
                  flat
                  color="primary"
                  icon="delete_forever"
                  @click.stop="deleteActionClick(tableBodyProps.row)"
                />
                <q-btn
                  v-if="actionSettings.editButton"
                  color="primary"
                  icon="edit"
                  @click.stop="editActionClick(tableBodyProps.row)"
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

    <q-dialog v-model="confirmationShow" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <span class="q-ml-sm">{{ confirmationText }}</span>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn v-close-popup flat label="Отмена" color="primary" />
          <q-btn
            v-close-popup
            flat
            label="Подтвердить"
            color="primary"
            @click="confirmationCallback"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>
